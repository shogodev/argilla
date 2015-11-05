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
Yii::import('backend.components.BApplication');
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

  public $clear = false;

  public $dstTables = array(
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

  public $parameterVariantsDelimiter = ',';

  private $allProductsAmount = 0;

  private $successWriteProductsAmount = 0;

  private $skipProductsAmount = 0;

  private $modelsCache = array();

  private $parameterNamesCache = array();

  private $parameterVariantsCache = array();

  private $urlCache;

  public function write(array $data, $uniqueIndex = null)
  {
    $itemsAmount = count($data);
    if( $itemsAmount == 0 )
      return;

    $this->allProductsAmount = $itemsAmount;

    if( $this->clear && !empty($this->dstTables) )
    {
      $this->logger->log('Очистка БД');
      $this->clearAll();
      $this->clear = false;
    }

    $progress = new ConsoleProgressBar($itemsAmount);
    $this->logger->log('Начало записи в БД');
    $progress->start();
    foreach($data as $item)
    {
      try
      {
        $this->writeItem($item);
      }
      catch(WarningException $e)
      {
        $this->logger->warning($e->getMessage());
      }
      $progress->setValueMap('memory', Yii::app()->format->formatSize(memory_get_usage()));
      $progress->advance();
    }
    $progress->finish();
    $this->logger->log('Записано '.$this->successWriteProductsAmount.' продуктов из '.$this->allProductsAmount.' (пропущено '.$this->skipProductsAmount.')');
    $this->logger->log('Записи в БД завершена');
  }

  private function writeItem(array $item)
  {
    /**
     * @var BProduct $product
     */
    if( $product = BProduct::model()->findByAttributes(array($item['uniqueAttribute'] => $item['uniqueIndex'])) )
    {
      $this->skipProductsAmount++;

      //$product->save();
      return;
    }

    $product = new BProduct('convert');
    $product->setAttributes($item['product'], false);
    $product->url = $this->createUniqueUrl($product->url);
    $product->visible = 1;

    foreach($item['assignment'] as $attribute => $value)
    {
      $modelName = BProductStructure::getModelName($attribute);
      $setAttributes = isset($this->assignmentTree[$attribute]) ? array('parent_id' => $product->{$this->assignmentTree[$attribute]}) : array();
      $associationId = $this->getModel($modelName, $value, $setAttributes);
      $this->setAttribute($product, $attribute, $associationId);
    }

    if( !$product->save() )
      throw new ImportModelValidateException($product, 'Не удалось создать продукт (строка '.$item['rowIndex'].' файл '.$item['file'].')');

    $this->successWriteProductsAmount++;

    $this->saveModifications($product, $item['modification']);

    if( !empty($this->assignment) )
      BProductAssignment::model()->saveAssignments($product, Arr::extract($product, $this->assignment));

    $this->saveParameters($product, $item['parameter']);
  }

  protected function saveModifications($parentProduct, $modifications = array())
  {
    foreach($modifications as $item)
    {
      $product = new BProduct('modification');
      $product->setAttributes($item['product'], false);
      $product->url = $this->createUniqueUrl($product->url);
      $product->parent = $parentProduct->id;
      $product->visible = 1;

      if( !$product->save() )
        throw new ImportModelValidateException($product, 'Не удалось создать модификацию product_id='.$parentProduct->id);
    }
  }

  /**
   * @param string $modelName
   * @param string $name
   * @param array $attributes
   *
   * @return int
   * @throws ImportModelValidateException
   */
  private function getModel($modelName, $name, array $attributes = array())
  {
    if( empty($name) )
      return 0;

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

    return $model->id;
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

  private function saveParameters(BProduct $product, $data)
  {
    foreach($data as $name => $value)
    {
      if( $value == '' )
        continue;

      $parameterName = $this->getParameterName($name);

      $value = strpos($value, $this->parameterVariantsDelimiter) !== false ? explode($this->parameterVariantsDelimiter, $value) : array($value);
      $value = Arr::trim($value);

      foreach($value as $variantName)
      {
        try
        {
          $this->saveVariant($variantName, $parameterName, $product);
        }
        catch(WarningException $e)
        {
          $this->logger->warning($e->getMessage());
        }
      }
    }
  }

  private function getParameterName($name)
  {
    if( !$parameter = Arr::get($this->parameterNamesCache, $name, BProductParamName::model()->findByAttributes(array('name' => $name))) )
    {
      $parameter = new BProductParamName();
      $parameter->parent = 2;
      $parameter->name = $name;
      $parameter->type = 'checkbox';

      if( !$parameter->save($parameter) )
      {
        throw new WarningException('Ошибка при создании параметра '.$name);
      }

      $this->parameterNamesCache[$name] = $parameter;
    }

    return $parameter;
  }

  /**
   * @param $variantName
   * @param BProductParamName $parameterName
   * @param BProduct $product
   *
   * @throws ImportModelValidateException
   * @throws WarningException
   */
  private function saveVariant($variantName, BProductParamName $parameterName, BProduct $product)
  {
    $parameter = new BProductParam();
    $parameter->param_id = $parameterName->id;
    $parameter->product_id = $product->id;

    if( $parameterName->type === 'checkbox' )
    {
      $parameter->variant_id = $this->getVariantId($parameterName->id, $variantName, $product->id);
    }
    else
    {
      $parameter->value = $variantName;
    }

    try
    {
      if( !$parameter->save() )
      {
        throw new ImportModelValidateException($parameter, 'Ошибка при создании параметра продукта id = '.$product->id);
      }
    }
    catch(CDbException $e)
    {
      throw new WarningException('Ошибка при создании параметра продукта id = '.$product->id.' '.$e->getMessage(), $e->getCode());
    }
  }

  private function getVariantId($paramId, $name, $productId)
  {
    if( !$variant = Arr::get($this->parameterVariantsCache, $paramId.$name, BProductParamVariant::model()->findByAttributes(array('param_id' => $paramId, 'name' => $name))) )
    {
      $variant = new BProductParamVariant();
      $variant->param_id = $paramId;
      $variant->name = $name;

      if( !$variant->save() )
        throw new ImportModelValidateException($variant, 'Ошибка при создании варианта для параметра '.$name.' id = '.$paramId.' продукт id = '.$productId);

      $this->parameterVariantsCache[$paramId.$name] = $variant;
    }

    return $variant->id;
  }

  protected function clearAll()
  {
    foreach($this->dstTables as $table)
    {
      $command = Yii::app()->db->createCommand(Yii::app()->db->schema->truncateTable($table));
      if( $command->execute() )
        throw new WarningException("Не удаллсь очистить таблицу ".$table);

      $this->logger->log('Таблица '.$table.' очищена');
    }
  }

  protected function clearParameterNames()
  {
    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $builder->createSqlCommand("DELETE FROM `{{product_param_name}}` WHERE id > 2")->execute();
    $builder->createSqlCommand("ALTER TABLE `{{product_param_name}}` AUTO_INCREMENT = 3")->execute();
  }

  protected function createUniqueUrl($url)
  {
    if( is_null($this->urlCache) )
    {
      $this->urlCache = array();

      $criteria = new CDbCriteria();
      $criteria->select = 'url';
      $command = Yii::app()->db->schema->commandBuilder->createFindCommand(BProduct::model()->tableName(), $criteria);

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