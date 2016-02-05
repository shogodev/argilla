<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('frontend.share.*');
Yii::import('frontend.share.behaviors.*');
Yii::import('frontend.share.helpers.*');
Yii::import('frontend.share.validators.*');
Yii::import('backend.components.*');
Yii::import('backend.components.db.*');
Yii::import('backend.components.interfaces.*');
Yii::import('backend.models.behaviors.*');
Yii::import('backend.modules.product.models.*');
Yii::import('backend.modules.product.models.behaviors.*');
Yii::import('backend.modules.product.components.*');
Yii::import('frontend.extensions.upload.components.*');

Yii::import('backend.modules.product.modules.import.components.exceptions.*');
Yii::import('backend.modules.product.modules.import.components.abstracts.AbstractImportWriter');

class ImportProductWriter extends AbstractImportWriter
{
  public $assignmentTree = array();

  public $assignment = array();

  public $clearTables = array(
    '{{product}}',
    '{{product_assignment}}',
    '{{product_param}}',
    '{{product_param_variant}}',

    '{{product_tree_assignment}}',
    '{{product_section}}',
    '{{product_type}}',
    '{{product_category}}',
    '{{product_collection}}',
  );

  /**
   * @var int $defaultCommonParameterGroup - id группы общих параметров
   */
  public $defaultCommonParameterGroup = 2;

  public $importScenario = 'import';

  public $importModificationScenario = BModificationBehavior::SCENARIO_MODIFICATION;

  private $allProductsAmount = 0;

  private $successWriteProductsAmount = 0;

  private $skipProductsAmount = 0;

  private $modelsCache = array();

  private $parameterNamesCache = array();

  private $parameterVariantsCache = array();

  private $urlCache;

  private $parameterGroupCache = array();

  public function init()
  {
    parent::init();

    $this->allProductsAmount = 0;

    $this->successWriteProductsAmount = 0;

    $this->skipProductsAmount = 0;
  }

  public function writeAll(array $data)
  {
    if( empty($data) )
      return;

    $itemsAmount = count($data);
    $this->allProductsAmount = $itemsAmount;

    $progress = new ConsoleProgressBar($itemsAmount);
    $this->logger->log('Начало записи в БД');
    $progress->start();
    foreach($data as $item)
    {
      $this->safeWriteItem($item);
      $progress->setValueMap('memory', Yii::app()->format->formatSize(memory_get_usage()));
      $progress->advance();
    }
    $progress->finish();
  }

  public function writePartial(array $data)
  {
    if( empty($data) )
      return;

    foreach($data as $item)
    {
      $this->allProductsAmount++;
      $this->safeWriteItem($item);
    }
  }

  public function showStatistics()
  {
    $this->logger->log('Записано '.$this->successWriteProductsAmount.' продуктов из '.$this->allProductsAmount.' (пропущено '.$this->skipProductsAmount.')');
    $this->logger->log('Записи в БД завершена');
  }

  protected function safeWriteItem($item)
  {
    try
    {
      $this->write($item);
    }
    catch(WarningException $e)
    {
      $itemId = '';

      if( !empty($item['uniqueIndex']) && !empty($item['uniqueAttribute']) )
        $itemId = ' '.$item['uniqueAttribute'].'='.$item['uniqueIndex'];

      $this->logger->warning($e->getMessage().$itemId);
    }
  }

  protected function write(array $item)
  {
    /**
     * @var BProduct $product
     */
    $product = ImportHelper::getModelWithoutBehaviors('BProduct', $this->importScenario);

    /**
     * @var BProduct $product
     */
    if( $foundProduct = $product->findByAttributes(array($item['uniqueAttribute'] => $item['uniqueIndex'])) )
    {
      $foundProduct->scenario = !empty($product->parent) ? $this->importModificationScenario : $this->importScenario;
      $foundProduct->detachBehaviors(); // детачим поведения подключенные в populateRecord

      $this->skipProductsAmount++;

      //$foundProduct->save();
      return;
    }

    $product->setAttributes($item['product'], false);
    $product->url = $this->createUniqueUrl($product->url);
    $product->visible = 1;

    $sectionModel = $this->prepareAssignmentAndGetSectionModel($product, $item);

    if( !$product->save() )
      throw new ImportModelValidateException($product, 'Не удалось создать продукт (строка '.$item['rowIndex'].' файл '.$item['file'].')');

    $this->saveModifications($product, $item['modification'], $sectionModel);

    if( !empty($this->assignment) )
    {
      BProductAssignment::model()->saveAssignments($product, Arr::extract($product, $this->assignment, array()));
    }

    $this->saveParameters($product, $item['parameter'], $sectionModel);

    if( !empty($item['basketParameter']) )
      $this->saveParameters($product, $item['basketParameter'], $sectionModel);

    $this->successWriteProductsAmount++;
  }

  protected function prepareAssignmentAndGetSectionModel(&$product, array $item)
  {
    $sectionModel = null;
    foreach($item['assignment'] as $attribute => $assignment)
    {
      $modelName = BProductStructure::getModelName($attribute);
      $setAttributes = isset($this->assignmentTree[$attribute]) ? array('parent_id' => $product->{$this->assignmentTree[$attribute]}) : array();

      $values = !is_array($assignment) ? array($assignment) : $assignment;

      $associationModels = array();
      foreach($values as $value)
      {
        if( $associationModel = $this->getModel($modelName, $value, $setAttributes) )
        {
          $associationModels[$value] = $associationModel;
          if( $associationModel instanceof BProductSection )
            $sectionModel = $associationModel;
        }
      }

      if( count($associationModels) == 0 )
        $attributeValue = null;
      else if( count($associationModels) == 1 )
        $attributeValue = reset($associationModels)->id;
      else
      {
        $attributeValue = array_keys(CHtml::listData($associationModels, 'id', 'id'));
      }

      $this->setAttribute($product, $attribute, $attributeValue);
    }

    return $sectionModel;
  }

  protected function saveModifications($parentProduct, $modifications = array(), $sectionModel)
  {
    foreach($modifications as $item)
    {
      /**
       * @var BProduct $product
       */
      $product = ImportHelper::getModelWithoutBehaviors('BProduct', $this->importModificationScenario);
      $product->setAttributes($item['product'], false);
      $product->url = $this->createUniqueUrl($product->url);
      $product->parent = $parentProduct->id;
      $product->visible = 1;

      if( !$product->save() )
        throw new ImportModelValidateException($product, 'Не удалось создать модификацию product_id='.$parentProduct->id);

      $this->saveParameters($product, $item['parameter'], $sectionModel);
    }
  }

  /**
   * @param string $modelName
   * @param string $name
   * @param array $attributes
   *
   * @return BActiveRecord
   * @throws ImportModelValidateException
   */
  private function getModel($modelName, $name, array $attributes = array())
  {
    if( empty($name) )
      return null;

    /**
     * @var BActiveRecord $model
     */
    if( !($model = Arr::get($this->modelsCache, $modelName.$name, $modelName::model()->findByAttributes(array('name' => $name)))) )
    {
      $model = new $modelName;
      $model->name = $name;

      $this->setAttribute($model, 'url', Utils::translite($name));
      $this->setAttribute($model, 'visible', 1);

      foreach($attributes as $attribute => $value)
      {
        $this->setAttribute($model, $attribute, $value);
      }

      if( !$model->save() )
      {
        throw new ImportModelValidateException($model, 'Ошибка при создании модели '.$modelName.' - '.$name);
      }

      $this->modelsCache[$modelName.$name] = $model;
    }

    return $model;
  }

  private function setAttribute(BActiveRecord $model, $attribute, $value)
  {
    try
    {
      $model->$attribute = $value;
    }
    catch(CException $e)
    {
    }
  }

  private function saveParameters(BProduct $product, $data, $sectionModel = null)
  {
    foreach($data as $attributes)
    {
      if( empty($attributes['name']) || empty($attributes['value']) )
        continue;

      try
      {
        $parameterGroupId = $this->getParameterGroupId($sectionModel, Arr::cut($attributes, 'common'));
        $parameterName = $this->getParameterName(Arr::cut($attributes, 'name'), Arr::cut($attributes, 'type'), $parameterGroupId, Arr::cut($attributes, 'key'));
      }
      catch(WarningException $e)
      {
        throw new WarningException($e->getMessage().' product_id = '.$product->id);
      }

      foreach($this->prepareVariantValues(Arr::cut($attributes, 'value')) as $variant)
      {
        try
        {
          $this->saveProductParameter($variant, $attributes, $parameterName, $product);
        }
        catch(WarningException $e)
        {
          $this->logger->warning($e->getMessage().' product_id = '.$product->id);
        }
      }
    }
  }

  private function prepareVariantValues($data)
  {
    if( $data == '' || (is_array($data) && empty($data)) )
      return array();

    if( !is_array($data) )
    {
      $values = array($data);
    }
    else
    {
      $values = $data;
    }

    return Arr::trim($values);
  }

  private function saveProductParameter($variantName, $attributes, BProductParamName $parameterName, BProduct $product)
  {
    $parameter = new BProductParam();
    $parameter->setAttributes($attributes, false);
    $parameter->param_id = $parameterName->id;
    $parameter->product_id = $product->id;

    if( $parameterName->type === 'checkbox' )
    {
      $parameter->variant_id = $this->getVariantId($parameterName->id, $variantName);
    }
    else
    {
      $parameter->value = $variantName;
    }

    try
    {
      if( !$parameter->save() )
      {
        throw new ImportModelValidateException($parameter, 'Ошибка при создании параметра продукта');
      }
    }
    catch(CDbException $e)
    {
      throw new WarningException('Ошибка при создании параметра parameter_id = '.$parameterName->id.' '.$e->getMessage(), $e->getCode());
    }
  }

  /**
   * @param $name
   * @param string $type
   * @param integer $parameterGroupId
   * @param string $key
   *
   * @return mixed
   * @throws WarningException
   */
  private function getParameterName($name, $type = 'checkbox', $parameterGroupId, $key = '')
  {
    $cashKey = mb_strtolower($name).$parameterGroupId;
    $parameter = Arr::get($this->parameterNamesCache, $cashKey);
    if( !$parameter )
    {
      $criteria = new CDbCriteria();
      $criteria->compare('name', $name);
      $criteria->compare('parent', '>'.BProductParamName::ROOT_ID);
      $criteria->compare('parent', $parameterGroupId);
      $parameter = BProductParamName::model()->find($criteria);
    }

    if( !$parameter )
    {
      $parameterName = new BProductParamName();
      $parameterName->parent = $parameterGroupId;
      $parameterName->name = $name;
      $parameterName->type = $type;
      $parameterName->key = $key;

      if( !$parameterName->save() )
      {
        throw new WarningException('Ошибка при создании параметра '.$name);
      }

      $this->parameterNamesCache[$cashKey] = $parameterName;
    }

    return $this->parameterNamesCache[$cashKey];
  }

  /**
   * @param BProductSection|null $sectionModel
   * @param bool $common
   *
   * @return BProductParamName
   * @throws WarningException
   */
  private function getParameterGroupId($sectionModel = null, $common = false)
  {
    if( $common )
      return $this->defaultCommonParameterGroup;

    if(  !($sectionModel instanceof BProductSection) )
      return $this->defaultCommonParameterGroup;

    if( !isset($this->parameterGroupCache[$sectionModel->id]) )
    {
      $parameterName = new BProductParamName();
      $parameterName->parent = 1;
      $parameterName->name = $sectionModel->name;

      if( !$parameterName->save() )
      {
        throw new WarningException('Ошибка при создании группы параметров '.$parameterName->name);
      }
      $this->saveParameterAssignment($parameterName, array('section_id' => $sectionModel->id));

      $this->parameterGroupCache[$sectionModel->id] = $parameterName;
    }

    return $this->parameterGroupCache[$sectionModel->id]->id;
  }

  private function saveParameterAssignment(BProductParamName $parameterName, array $attributes)
  {
    if( empty($attributes['section_id']) )
      return;

    $parameterAssignment = new BProductParamAssignment();
    $parameterAssignment->setAttributes($attributes);
    $parameterAssignment->param_id = $parameterName->id;

    if( !$parameterAssignment->save() )
    {
      throw new WarningException('Ошибка при создании ParameterAssignment для парамера '.$parameterName->name);
    }
  }

  private function getVariantId($paramId, $name)
  {
    if( !$variant = Arr::get($this->parameterVariantsCache, $paramId.$name, BProductParamVariant::model()->findByAttributes(array('param_id' => $paramId, 'name' => $name))) )
    {
      $variant = new BProductParamVariant();
      $variant->param_id = $paramId;
      $variant->name = $name;

      try
      {
        if( !$variant->save() )
          throw new ImportModelValidateException($variant, 'Ошибка при создании варианта для параметра '.$name.' id = '.$paramId);
      }
      catch(CDbException $e)
      {
        if( strpos($e->getMessage(), 'CDbCommand failed to execute the SQL statement: SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry') !== false )
        {
          throw new WarningException('Дублирование варианта "'.$name.'"');
        }
        else
        {
          throw $e;
        }
      }

      $this->parameterVariantsCache[$paramId.$name] = $variant;
    }

    return $variant->id;
  }

  protected function createUniqueUrl($url)
  {
    if( is_null($this->urlCache) )
    {
      $this->urlCache = array();

      $criteria = new CDbCriteria();
      $criteria->select = 'url';
      $command = Yii::app()->db->schema->commandBuilder->createFindCommand('{{product}}', $criteria);

      foreach($command->queryColumn() as $itemUrl)
        $this->urlCache[$itemUrl] = $itemUrl;
    }

    $uniqueUrl = Utils::translite(trim($url));
    $suffix = 1;
    while(isset($this->urlCache[$uniqueUrl]))
    {
      $uniqueUrl = $url.'_'.$suffix++;
    }

    $this->urlCache[$uniqueUrl] = $uniqueUrl;

    return $uniqueUrl;
  }
}