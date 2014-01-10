<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
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
   * @var ProductFilter[]|null $filters
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
    'popular_up' => 'IF(position=0, 1, 0), position DESC ',
    'popular_down' => 'IF(position=0, 1, 0), position ASC ',
    'price_up' => 'IF(price=0, 1, 0), price DESC ',
    'price_down' => 'IF(price=0, 1, 0), price ASC',
    'name_up' => 'name DESC',
    'name_down' => 'name ASC',
    'available_up' => 'dump DESC',
    'available_down' => 'dump ASC',
  );

  /**
   * @var $products FActiveDataProvider
   */
  protected $products;

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
  }

  /**
   * @return FActiveDataProvider
   */
  public function getDataProvider()
  {
    return $this->buildDataProvider($this->getFilterCriteria());
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
    }

    return $this->filteredCriteria;
  }

  protected function initCriteria(CDbCriteria $criteria)
  {
    $assignment = ProductAssignment::model()->tableName();
    $criteria->join  = 'JOIN '.$assignment.' AS a ON a.product_id = t.id';
    $criteria->order = Arr::get(self::$sortingRange, $this->sorting, Arr::reset(self::$sortingRange));
    $criteria->compare('t.visible', 1);
    $criteria->compare('a.visible', 1);

    $this->criteria = $criteria;
  }

  protected function buildDataProvider(CDbCriteria $criteria)
  {
    $config = array('criteria' => $criteria);
    if( !$this->pagination )
      $config['pagination'] = false;

    $this->products = new FActiveDataProvider('Product', $config);
    $this->products->attachEventHandler('onAfterFetchData', array($this, 'afterFetchData'));

    return $this->products;
  }

  protected function afterFetchData($event)
  {
    if( $this->fetchContent )
    {
      $this->setImages();
      $this->setParameters();
    }
  }

  protected function setImages()
  {
    /**
     * @var $products Product[]
     */
    $products = $this->products->getData();
    $criteria = new CDbCriteria();
    $criteria->addInCondition('parent', $this->products->getKeys());
    $productImages = ProductImage::model()->findAll($criteria);

    $images = array();
    foreach($productImages as $image)
      $images[$image['parent']][$image['type']][] = $image;

    foreach($products as $product)
      if( isset($images[$product->id]) )
        foreach($images[$product->id] as $type => $imgs)
          $product->setImages($imgs, $type);
  }

  protected function setParameters()
  {
    /**
     * @var $products Product[]
     */
    $products = $this->products->getData();
    $criteria = new CDbCriteria();
    $criteria->compare('t.section', '1');

    $names      = ProductParameterName::model()->setGroupCriteria($criteria)->search();
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

  protected function setRelations($relationName, $sortFunction = null)
  {
    $p = new Product();
    $md = $p->getMetaData();
    $relation = $md->relations[$relationName];
    $className = $relation->className;

    $criteria = new CDbCriteria();
    $criteria->addInCondition('product_id', $this->products->getKeys());
    $relatedModels = $className::model()->findAll($criteria);

    $models = array();
    foreach($relatedModels as $model)
    {
      $models[$model['product_id']][] = $model;
    }

    /**
     * @var $product Product
     */
    foreach($this->products->getData() as $product)
    {
      if( !empty($models[$product->id]) )
      {
        if( is_callable($sortFunction) )
          usort($models[$product->id], $sortFunction);

        foreach($models[$product->id] as $model)
        {
          $product->addRelatedRecord($relationName, $model, true);
        }
      }
    }
  }
}