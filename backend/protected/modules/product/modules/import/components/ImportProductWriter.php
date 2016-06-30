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

  protected $successWriteProductsAmount = 0;

  protected $parameterNamesCache = array();

  protected $parameterGroupCache = array();

  private $allProductsAmount = 0;

  private $skipProductsAmount = 0;

  private $modelsCache = array();

  private $parameterVariantsCache = array();

  public function beforeProcessNewFile()
  {
    parent::beforeProcessNewFile();

    $this->allProductsAmount = 0;

    $this->successWriteProductsAmount = 0;

    $this->skipProductsAmount = 0;

    $this->parameterNamesCache = array();

    $this->parameterGroupCache = array();

    $this->modelsCache = array();

    $this->parameterVariantsCache = array();

    ImportHelper::clearUrlCache(null);
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
     * @var BProduct $newProductModel
     * @var BProduct $product
     */

    $newProductModel = ImportHelper::getModelWithoutBehaviors('BProduct', $this->importScenario);

    if( $product = $newProductModel->findByAttributes(array($item['uniqueAttribute'] => $item['uniqueIndex'])) )
    {
      $this->updateProduct($product, $item);
    }
    else
    {
      $this->saveNewProduct($newProductModel, $item);
    }
  }

  protected function saveNewProduct(BProduct $newProductModel, array $item)
  {
    $newProductModel->setAttributes($item['product'], false);
    $newProductModel->url = ImportHelper::createUniqueUrl($newProductModel->tableName(), $newProductModel->url, true);

    $sectionModel = $this->prepareAssignmentAndGetSectionModel($newProductModel, $item);

    if( !$newProductModel->save() )
      throw new ImportModelValidateException($newProductModel, 'Не удалось создать продукт (строка '.$item['rowIndex'].' файл '.$item['file'].')');

    $this->saveModifications($newProductModel, $item['modification'], $sectionModel);

    if( !empty($this->assignment) )
    {
      BProductAssignment::model()->saveAssignments($newProductModel, Arr::extract($newProductModel, $this->assignment, array()));
    }

    $this->saveParameters($newProductModel, $item['parameter'], $sectionModel);

    if( !empty($item['basketParameter']) )
      $this->saveParameters($newProductModel, $item['basketParameter'], $sectionModel);

    $this->successWriteProductsAmount++;
  }

  protected function updateProduct(BProduct $product, array $item)
  {
    $product->scenario = !empty($product->parent) ? $this->importModificationScenario : $this->importScenario;
    $product->detachBehaviors();

    $this->skipProductsAmount++;
    //$product->save();
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
        if( $associationModel = $this->getProductAssignmentModel($modelName, $value, $setAttributes) )
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
      $product->url = ImportHelper::createUniqueUrl($product->tableName(), $product->url, true);
      $product->parent = $parentProduct->id;

      if( !$product->save() )
        throw new ImportModelValidateException($product, 'Не удалось создать модификацию product_id='.$parentProduct->id);

      $this->saveParameters($product, $item['parameter'], $sectionModel);
    }
  }

  protected function saveParameters(BProduct $product, $data, $sectionModel = null)
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

  /**
   * @param $name
   * @param string $type
   * @param integer $parameterGroupId
   * @param string $key
   *
   * @return mixed
   * @throws WarningException
   */
  protected function getParameterName($name, $type = 'checkbox', $parameterGroupId, $key = '')
  {
    $cashKey = mb_strtolower($name).$parameterGroupId;
    $parameterName = Arr::get($this->parameterNamesCache, $cashKey);
    if( !$parameterName )
    {
      $criteria = new CDbCriteria();
      $criteria->compare('name', $name);
      $criteria->compare('parent', '>'.BProductParamName::ROOT_ID);
      $criteria->compare('parent', $parameterGroupId);
      $parameterName = BProductParamName::model()->find($criteria);
    }

    if( !$parameterName )
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
    }

    $this->parameterNamesCache[$cashKey] = $parameterName;

    return $this->parameterNamesCache[$cashKey];
  }

  /**
   * @param BProductSection|null $sectionModel
   * @param bool $common
   *
   * @return BProductParamName
   * @throws WarningException
   */
  protected function getParameterGroupId($sectionModel = null, $common = false)
  {
    if( $common )
      return $this->defaultCommonParameterGroup;

    if( !($sectionModel instanceof BProductSection) )
      return $this->defaultCommonParameterGroup;

    if( !isset($this->parameterGroupCache[$sectionModel->id]) )
    {
      $parameterName = BProductParamName::model()->findByAttributes(array('parent' => BProductParamName::ROOT_ID, 'name' => $sectionModel->name));

      if( !$parameterName )
      {
        $parameterName = new BProductParamName();
        $parameterName->parent = BProductParamName::ROOT_ID;
        $parameterName->name = $sectionModel->name;

        if( !$parameterName->save() )
        {
          throw new WarningException('Ошибка при создании группы параметров '.$parameterName->name);
        }
        $this->saveParameterAssignment($parameterName, array('section_id' => $sectionModel->id));
      }

      $this->parameterGroupCache[$sectionModel->id] = $parameterName;
    }

    return $this->parameterGroupCache[$sectionModel->id]->id;
  }

  /**
   * @param string $modelName
   * @param string $name
   * @param array $attributes
   *
   * @return BActiveRecord
   * @throws ImportModelValidateException
   */
  private function getProductAssignmentModel($modelName, $name, array $attributes = array())
  {
    if( empty($name) )
      return null;

    $cacheKey = $modelName.$name.serialize($attributes);

    /**
     * @var BActiveRecord $model
     */
    if( !($model = Arr::get($this->modelsCache, $cacheKey, $this->tryFindProductAssignmentModel($modelName, $name, $attributes))) )
    {
      $model = new $modelName;
      $model->name = $name;

      $this->setAttribute($model, 'url', ImportHelper::createUniqueUrl($model->tableName(), Utils::translite($name)));
      $this->setAttribute($model, 'visible', 1);

      foreach($attributes as $attribute => $value)
      {
        $this->setAttribute($model, $attribute, $value);
      }

      if( !$model->save() )
      {
        throw new ImportModelValidateException($model, 'Ошибка при создании модели '.$modelName.' - '.$name);
      }

      $this->modelsCache[$cacheKey] = $model;
    }

    return $model;
  }

  /**
   * @param $modelName
   * @param $name
   * @param array $attributes
   *
   * @return BProductStructure|BTreeAssignmentBehavior|bool
   */
  private function tryFindProductAssignmentModel($modelName, $name, array $attributes = array())
  {
    /**
     * @var BProductStructure|BTreeAssignmentBehavior $model
     */
    $model = new $modelName;

    if( $model->asa('tree') && isset($attributes['parent_id']) )
    {
      $model->parent_id = $attributes['parent_id'];
    }

    $model->visible = null;

    $criteria = new CDbCriteria();
    $criteria->compare('t.name', $name);
    $data = $model->search($criteria)->getData();

    return Arr::reset($data);
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
}