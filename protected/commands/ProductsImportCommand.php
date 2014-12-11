<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('frontend.commands.AbstractConvertCommand');
Yii::import('frontend.share.helpers.*');
Yii::import('frontend.share.formatters.*');
Yii::import('backend.models.behaviors.*');
Yii::import('backend.modules.product.models.*');
Yii::import('backend.modules.product.models.behaviors.*');


class ProductsImportCommand extends AbstractConvertCommand
{
  const FILE_ID = 'file_id';

  const ARTICUL = 0;
  const ENABLED = 1;
  const NAME = 2;

  const TMP1 = 3;

  const SECTION = 4;
  const TYPE = 5;
  const COLLECTION = 8;
  const CATEGORY = 9;

  const NOTICE = 16;

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

  private $headers = array();

  private $models = array();

  private $parameterNames = array();

  private $parameterVariants = array();

  /**
   * @var array
   */
  private $files;

  public function actionIndex($mode = 'update', $file = '')
  {
    if( !empty($file) )
    {
      $this->files = array($file);
    }

    parent::actionIndex($mode);
    $this->reindex();
  }

  protected function clearAll()
  {
    parent::clearAll();
    //$this->clearParameterNames();
  }

  protected function clearParameterNames()
  {
    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $builder->createSqlCommand("DELETE FROM `{{product_param_name}}` WHERE id > 2")->execute();
    $builder->createSqlCommand("ALTER TABLE `{{product_param_name}}` AUTO_INCREMENT = 3")->execute();
  }

  protected function findAll()
  {
    $data = array();

    foreach($this->getFiles() as $i => $file)
    {
      $row = 0;
      $handle = fopen($file, 'r');

      while( ($item = fgetcsv($handle)) !== false )
      {
        if( $row++ == 0 )
        {
          $this->headers[$i] = $item;
          continue;
        }

        if( !empty($item[self::ARTICUL]) )
        {
          $item = $this->formatItem($item);
          $item[self::FILE_ID] = $i;
          $data[$item[self::ARTICUL]] = $item;
        }
      }

      fclose($handle);
    }

    $this->logger->updateStatus(array('total_items' => count($data)));

    return $data;
  }

  /**
   * @param mixed $data
   *
   * @return bool $result
   *
   * $product = BProduct::model()->findByPk($data['id']);
   * if( $product )
   *   return true;
   *
   * $product       = new Product('convert');
   * $product->id   = $data['id'];
   * $product->name = $data['name'];
   * return $product->save($product);
   */
  protected function create($data)
  {
    /**
     * @var BProduct $product
     */
    if( $product = BProduct::model()->findByAttributes(array('articul' => $data[self::ARTICUL])) )
    {
      $this->logger->updateStatus(array('processed' => $product->id));
      return $this->save($product);
    }

    $product = new BProduct('convert');
    $product->name = $data[self::NAME];
    $product->articul = $data[self::ARTICUL];
    $product->url = Utils::translite($data[self::ARTICUL]);
    $product->notice = $data[self::NOTICE];
    $product->visible = 1;
    $product->dump = 1;

    $product->section_id = $this->getModel('BProductSection', $data[self::SECTION]);
    $product->type_id = $this->getModel('BProductType', $data[self::TYPE], array('parent_id' => $product->section_id));
    $product->category_id = $this->getModel('BProductCategory', $data[self::CATEGORY]);
    $product->collection_id = $this->getModel('BProductCollection', $data[self::COLLECTION]);

    $result = $this->save($product);

    if( $result )
    {
      $this->saveAssignments($product);
      $this->saveParameters($product, $data);

      $this->logger->updateStatus(array('processed' => $product->id));
    }

    return $result;
  }

  private function formatItem($item)
  {
    foreach($item as $key => $value)
    {
      $item[$key] = trim($value, '"\' ');
    }

    return $item;
  }

  /**
   * @param string $modelName
   * @param string $name
   * @param array $attributes
   *
   * @throws CException
   *
   * @return integer
   */
  private function getModel($modelName, $name, array $attributes = array())
  {
    if( empty($name) )
      return 0;

    if( !($model = Arr::get($this->models, $modelName.$name, $modelName::model()->findByAttributes(array('name' => $name)))) )
    {
      $model = new $modelName;
      $model->name = $name;

      $this->setAttribute($model, 'url', Utils::translite($name));
      $this->setAttribute($model, 'visible', 1);

      foreach($attributes as $attribute => $value)
      {
        $this->setAttribute($model, $attribute, $value);
      }

      if( !$this->save($model) )
      {
        $this->logger->error('Ошибка при создании модели '.$modelName.' - '.$name);
      }

      $this->models[$modelName.$name] = $model;
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

  private function saveAssignments(BProduct $product)
  {
    $assignments = Arr::extract($product, array('section_id', 'type_id', 'category_id', 'collection_id'));
    BProductAssignment::model()->saveAssignments($product, $assignments);
  }

  private function saveParameters(BProduct $product, $data)
  {
    $reflect = new ReflectionClass(get_class($this));
    $constants = $reflect->getConstants();

    foreach($data as $key => $value)
    {
      if( in_array($key, $constants) || empty($value) || strpos($this->headers[$data[self::FILE_ID]][$key], 'не загружаем') !== false )
        continue;

      $parameterName = $this->getParameterName($this->headers[$data[self::FILE_ID]][$key]);

      $value = strpos($value, ',') !== false ? explode(',', $value) : array($value);
      $value = Arr::trim($value);

      foreach($value as $variantName)
      {
        $this->saveVariant($variantName, $parameterName, $product);
      }
    }
  }

  private function getParameterName($name)
  {
    if( !$parameter = Arr::get($this->parameterNames, $name, BProductParamName::model()->findByAttributes(array('name' => $name))) )
    {
      $parameter = new BProductParamName();
      $parameter->parent = 2;
      $parameter->name = $name;
      $parameter->type = 'checkbox';

      if( !$this->save($parameter, false) )
      {
        $this->logger->error('Ошибка при создании параметра '.$name);
      }

      $this->parameterNames[$name] = $parameter;
    }

    return $parameter;
  }

  /**
   * @param $variantName
   * @param BProductParamName $parameterName
   * @param BProduct $product
   */
  private function saveVariant($variantName, BProductParamName $parameterName, BProduct $product)
  {
    $parameter             = new BProductParam();
    $parameter->param_id   = $parameterName->id;
    $parameter->product_id = $product->id;

    if( $parameterName->type === 'checkbox' )
    {
      $parameter->variant_id = $this->getVariantId($parameterName->id, $variantName);
    }
    else
    {
      $parameter->value = $variantName;
    }

    if( !$this->save($parameter, false) )
    {
      $this->logger->error('Ошибка при создании параметра продукта');
    }
  }

  private function getVariantId($paramId, $name)
  {
    if( !$variant = Arr::get($this->parameterVariants, $paramId.$name, BProductParamVariant::model()->findByAttributes(array('param_id' => $paramId, 'name' => $name))) )
    {
      $variant = new BProductParamVariant();
      $variant->param_id = $paramId;
      $variant->name = $name;

      if( !$this->save($variant, false) )
      {
        $this->logger->error('Ошибка при создании варианта параметра '.$name);
      }

      $this->parameterVariants[$paramId.$name] = $variant;
    }

    return $variant->id;
  }

  private function getFiles()
  {
    if( empty($this->files) )
    {
      $this->files = array_reverse(CFileHelper::findFiles('f/prices', array(
        'fileTypes' => array('csv')
      )));
    }

    return $this->files;
  }

  private function reindex()
  {
    $runner = new CConsoleCommandRunner();
    $runner->commands = array(
      'indexer' => array(
        'class' => 'frontend.commands.IndexerCommand',
      ),
    );

    ob_start();
    $runner->run(array('yiic', 'indexer', 'refresh'));
    return ob_get_clean();
  }
}