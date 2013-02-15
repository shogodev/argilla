<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 12.10.12
 *
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
   * @var ProductFilter $filter
   */
  public $filter;

  /**
   * @var CDbCriteria $criteria
   */
  public $criteria;

  /**
   * Параметр, отвечающий за выборку параметров и картинок товаров,
   * можно отключить, если данные эти не используются, чтобы не тратить время на обработку
   *
   * @var bool
   */
  public $fetchContent = true;

  /**
   * Выбираем все параметры или только параметры для подбора, которые отображаются на разводных
   *
   * @var bool
   */
  public $fetchProductParameters = false;

  public static $sortingRange = array(
    'position_up' => 'IF(position=0, 1, 0), position DESC ',
    'position_down' => 'IF(position=0, 1, 0), position ASC ',
    'price_up' => 'IF(price=0, 1, 0), price DESC ',
    'price_down' => 'IF(price=0, 1, 0), price ASC',
    'name_up' => 'name DESC',
    'name_down' => 'name ASC',
    'weight_up' => 'weight DESC ',
    'weight_down' => 'weight ASC',
  );

  /**
   * @var $products FActiveDataProvider
   */
  protected $products;

  /**
   * @param CDbCriteria $criteria
   * @param null $sorting
   * @param bool $pagination
   * @param ProductFilter $filter
   */
  public function __construct(CDbCriteria $criteria, $sorting = null, $pagination = true, ProductFilter $filter = null)
  {
    $this->criteria   = $criteria;
    $this->sorting    = $sorting;
    $this->pagination = $pagination;
    $this->filter     = $filter;
  }

  /**
   * @return FActiveDataProvider
   */
  public function getProducts()
  {
    //todo: подумать куда это можно вынести
    $assignment = ProductAssignment::model()->tableName();
    $this->criteria->join  = 'JOIN '.$assignment.' AS a ON a.product_id = t.id';
    $this->criteria->order = Arr::get(self::$sortingRange, $this->sorting, Arr::reset(self::$sortingRange));

    if( $this->filter )
      $this->criteria = $this->filter->apply($this->criteria);

    $config = array('criteria' => $this->criteria);
    if( !$this->pagination )
      $config['pagination'] = false;

    $this->products = new FActiveDataProvider('Product', $config);
    $this->products->attachEventHandler('onAfterFetchData', array($this, 'afterFetchData'));

    return $this->products;
  }

  /**
   * @param CActiveRecord $model
   *
   * @return CActiveRecord
   */
  public function getPrevious(CActiveRecord $model)
  {
    if( !$this->products )
      $this->getProducts();

    $keys     = $this->products->getKeys();
    $previous = array_search($model->id, $keys) - 1;
    $previous = $previous < 0 ? $previous + count($keys) : $previous;

    return isset($keys[$previous]) ? $model->findByPk($keys[$previous]) : null;
  }

  /**
   * @param CActiveRecord $model
   *
   * @return CActiveRecord
   */
  public function getNext(CActiveRecord $model)
  {
    if( !$this->products )
      $this->getProducts();

    $keys = $this->products->getKeys();
    $next = array_search($model->id, $keys) + 1;
    $next = $next > count($keys) - 1 ? $next - count($keys) : $next;

    return isset($keys[$next]) ? $model->findByPk($keys[$next]) : null;
  }

  public function getCacheKey()
  {
    return sha1(serialize(array(
      $this->criteria->toArray(),
      $this->filter->getState(),
      $this->sorting,
    )));
  }

  public function getCacheDependency()
  {
    return new CDbCacheDependency("SELECT `value` FROM `{{settings}}` WHERE `param` = 'products_cache'");
  }

  protected function afterFetchData($event)
  {
    if( $this->fetchContent )
    {
      $this->getImages();
      $this->getParameters();
    }
  }

  protected function getImages()
  {
    $products = $this->products->getData();
    $keys     = $this->products->getKeys();

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

  protected function getParameters()
  {
    $products = $this->products->getData();
    $keys     = $this->products->getKeys();

    $criteria = new CDbCriteria();
    $criteria->compare('visible', '=1');

    // на разводных вибираем только параметры для подбора,
    // если установлено соответствующие свойство
    if( !$this->fetchProductParameters )
      $criteria->compare('t.section', '=1');

    $names      = ProductParamName::model()->search($criteria);
    $nameIds    = $names->getKeys();

    $params   = array();
    $paramIds = array();

    $data = Yii::app()->db->createCommand()
      ->selectDistinct()
      ->from(ProductParam::model()->tableName())
      ->where(array('AND', array('IN', 'product_id', $keys), array('IN', 'param_id', $nameIds)))
      ->query();

    foreach($data as $row)
    {
      $params[$row['product_id']][$row['param_id']][$row['variant_id']] = $row;
      $paramIds[] = $row['param_id'];
    }

    $variants = $this->getVariants($paramIds);

    foreach($products as $product)
    {
      $parameters = array();

      foreach($names->getData() as $name)
      {
        $paramName = clone $name;

        if( isset($params[$product->id][$paramName->id]) )
          foreach($params[$product->id][$paramName->id] as $value)
            $paramName->setValue($value);

        if( is_array($paramName->value) )
        {
          $variant = isset($variants[$paramName->id]) ? $variants[$paramName->id] : array();
          $paramName->value = $paramName->setVariants($variant);
        }

        $parameters[$paramName->id] = $paramName;
      }

      $product->parameters = $parameters;
    }
  }

  protected function getVariants(array $paramIds)
  {
    $variants = array();

    $data = Yii::app()->db->createCommand()
      ->from(ProductParamVariant::model()->tableName())
      ->where(array('IN', 'param_id', $paramIds))
      ->query();

    foreach($data as $row)
      $variants[$row['param_id']][$row['id']] = $row;

    return $variants;
  }
}