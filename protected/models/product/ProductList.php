<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
 * @property CDbCriteria $criteria
 * @property CDbCacheDependency cacheDependency
 */
class ProductList extends CComponent
{
  /**
   * @var string $sorting
   */
  public $sorting;

  /**
   * @var bool $pagination
   */
  public $pagination;

  /**
   * @var Filter[]|null $filters
   */
  public $filters = null;

  /**
   * Параметр, отвечающий за выборку параметров и картинок товаров,
   * можно отключить, если данные эти не используются, чтобы не тратить время на обработку
   *
   * @var bool
   */
  public $fetchContent = true;

  public static $sortingRange = array(
    'popular_up' => 'IF(t.position=0, 1, 0), t.position DESC, dump DESC, price',
    'popular_down' => 'IF(t.position=0, 1, 0), t.position ASC, dump DESC, price',
    'price_up' => 'IF(price=0, 1, 0), price DESC',
    'price_down' => 'IF(price=0, 1, 0), price ASC',
    'name_up' => 'name DESC',
    'name_down' => 'name ASC',
    'available_up' => 'dump DESC',
    'available_down' => 'dump ASC',
  );

  /**
   * @var CDbCriteria
   */
  public $parametersCriteria;

  /**
   * @var $products FActiveDataProvider
   */
  protected $dataProvider;

  /**
   * @var CDbCriteria $criteria
   */
  protected $criteria;

  protected $filteredCriteria;

  /**
   * @param CDbCriteria $criteria
   * @param null $sorting
   * @param bool $pagination
   * @param null $filters
   */
  public function __construct(CDbCriteria $criteria, $sorting = null, $pagination = true, $filters = null)
  {
    $this->sorting = $sorting;
    $this->pagination = $pagination;

    if( !empty($filters) )
      $this->filters = is_array($filters) ? $filters : array($filters);

    $this->initCriteria($criteria);

    $this->parametersCriteria = new CDbCriteria();
    $this->parametersCriteria->addColumnCondition(array('t.section' => 1, 't.section_list' => 1), 'OR');
  }

  /**
   * @param boolean $refresh
   * @return FActiveDataProvider
   */
  public function getDataProvider($refresh = false)
  {
    if( is_null($this->dataProvider) || $refresh )
      $this->buildDataProvider($this->getFilterCriteria());

    return $this->dataProvider;
  }

  /**
   * @return FActiveDataProvider
   */
  public function getRandomDataProvider()
  {
    $criteria = clone $this->getFilterCriteria();
    $criteria->condition = '';
    $criteria->params = array();

    $productIds = $this->getProductIds();

    if( $criteria->limit )
    {
      shuffle($productIds);
      $productIds = array_slice($productIds, 0, $criteria->limit);
    }

    $criteria->addInCondition('t.id', $productIds);

    return $this->buildDataProvider($criteria);
  }

  /**
   * @return array
   */
  public function getProductIds()
  {
    $productModel = Product::model();
    $builder = new CDbCommandBuilder(Yii::app()->db->getSchema());
    $command = $builder->createFindCommand($productModel->tableName(), $this->getFilterCriteria());

    return CHtml::listData($command->queryAll(),'id', 'id') ;
  }

  public function getCacheKey()
  {
    $arrayForSerialize = array(
      $this->criteria->toArray(),
      $this->sorting,
    );

    if( $this->filters )
      foreach($this->filters as $filter)
        $arrayForSerialize[] = $filter->getState();

    return sha1(serialize($arrayForSerialize));
  }

  public function getCacheDependency()
  {
    return new CDbCacheDependency("SELECT `value` FROM `{{settings}}` WHERE `param` = 'products_cache'");
  }

  public function getFilterCriteria()
  {
    if( $this->filteredCriteria == null )
    {
      $filteredCriteria = clone $this->criteria;

      if( $this->filters )
        foreach($this->filters as $filter)
          $filteredCriteria = $filter->apply($filteredCriteria);

      $this->filteredCriteria = $filteredCriteria;

      foreach(array('distinct', 'order', 'join') as $value)
        $this->filteredCriteria->$value = $this->criteria->$value;
    }

    return $this->filteredCriteria;
  }

  protected function initCriteria(CDbCriteria $criteria)
  {
    $assignment = ProductAssignment::model()->tableName();
    $criteria->join = 'JOIN '.$assignment.' AS a ON a.product_id = t.id '.$criteria->join;
    $criteria->distinct = true;
    $criteria->compare('t.visible', 1);
    $criteria->compare('a.visible', 1);

    if( empty($criteria->order) )
      $criteria->order = Arr::get(self::$sortingRange, $this->sorting, Arr::reset(self::$sortingRange));

    $this->criteria = $criteria;
  }

  protected function buildDataProvider(CDbCriteria $criteria)
  {
    $config = array('criteria' => $criteria);
    if( !$this->pagination )
      $config['pagination'] = false;

    $this->dataProvider = new FActiveDataProvider('Product', $config);
    $this->dataProvider->attachEventHandler('onAfterFetchData', array($this, 'afterFetchData'));

    return $this->dataProvider;
  }

  protected function afterFetchData($event)
  {
    if( $this->fetchContent )
    {
      $this->setImages();
      $this->setParameters();
      $this->setAssignments();
    }
  }

  protected function setImages()
  {
    $productImages = $this->findRecords('parent', 'ProductImage');

    $images = array();
    foreach($productImages as $image)
      $images[$image['parent']][$image['type']][] = $image;

    /**
     * @var $product Product
     */
    foreach($this->dataProvider->getData() as $product)
      if( isset($images[$product->id]) )
        foreach($images[$product->id] as $type => $imgs)
          $product->setImages($imgs, $type);
  }

  protected function setParameters()
  {
    /**
     * @var $products Product[]
     */
    $products = $this->dataProvider->getData();
    $names = ProductParameterName::model()->search($this->parametersCriteria);
    $parameters = array();

    foreach($products as $product)
    {
      foreach($names as $name)
      {
        $productParameterName = clone $name;
        $productParameterName->setProductId($product->id);
        $product->addParameter($productParameterName);
        $parameters[] = $productParameterName;
      }
    }

    ProductParameter::model()->setParameterValues($parameters);
  }

  protected function setAssignments()
  {
    $assignments = array('section', 'type', 'category');
    $criteria = new CDbCriteria(array('select' => 'a.product_id'));
    $criteria->addInCondition('product_id', $this->dataProvider->getKeys());
    $productAssignments = ProductAssignment::model()->getAssignments($criteria);

    foreach($assignments as $assignment)
    {
      $this->setAssignment($productAssignments, $assignment);
    }
  }

  protected function setAssignment($productAssignments, $modelName)
  {
    $models = array();
    $assignments = CHtml::listData($productAssignments, 'product_id', $modelName.'_id');
    $keys = array_unique(array_values($assignments));
    $records = $this->findRecords('id', 'Product'.ucfirst($modelName), $keys, new CDbCriteria(array('index' => 'id')));

    foreach($assignments as $product => $assignment)
    {
      $models[$product] = Arr::get($records, $assignment, null);
    }

    $this->setRecords($modelName, $models);
  }

  protected function setRelations($relationName)
  {
    $models = array();
    $relation = Product::model()->getMetaData()->relations[$relationName];
    $records = $this->findRecords($relation->foreignKey, $relation->className);

    foreach($records as $record)
    {
      if( get_class($relation) === 'CHasOneRelation' )
      {
        $models[$record['product_id']] = $record;
      }
      else
      {
        $models[$record['product_id']][] = $record;
      }
    }

    $this->setRecords($relationName, $models);
  }

  /**
   * @param string $fk
   * @param string $className
   * @param array $keys
   * @param CDbCriteria $criteria
   *
   * @return FActiveRecord[]
   */
  protected function findRecords($fk, $className, array $keys = null, CDbCriteria $criteria = null)
  {
    if( !isset($criteria) )
    {
      $criteria = new CDbCriteria();
    }

    $criteria->addInCondition($fk, isset($keys) ? $keys : $this->dataProvider->getKeys());
    return $className::model()->findAll($criteria);
  }

  /**
   * @param string $modelName
   * @param FActiveRecord[] $models
   */
  protected function setRecords($modelName, array $models)
  {
    /**
     * @var $product Product
     */
    foreach($this->dataProvider->getData() as $product)
    {
      $records = Arr::get($models, $product->id, array(null));

      if( is_object($records) )
      {
        $useIndex = false;
        $records = array($records);
      }

      foreach($records as $model)
      {
        $product->addRelatedRecord($modelName, $model, isset($useIndex) ? $useIndex : true);
      }
    }
  }
}