<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
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
   * @var ProductFilter[]|null $filters
   */
  public $filters = null;

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

  public static $sortingRange = array(
    'position_up' => 'IF(position=0, 1, 0), position DESC ',
    'position_down' => 'IF(position=0, 1, 0), position ASC ',
    'price_up' => 'IF(price=0, 1, 0), price DESC ',
    'price_down' => 'IF(price=0, 1, 0), price ASC',
    'name_up' => 'name DESC',
    'name_down' => 'name ASC',
  );

  /**
   * @var $products FActiveDataProvider
   */
  protected $products;

  /**
   * @param CDbCriteria $criteria
   * @param null $sorting
   * @param bool $pagination
   * @param null $filters
   */
  public function __construct(CDbCriteria $criteria, $sorting = null, $pagination = true, $filters = null)
  {
    $this->criteria   = $criteria;
    $this->sorting    = $sorting;
    $this->pagination = $pagination;
    if( !empty($filters) )
      $this->filters = is_array($filters) ? $filters : array($filters);
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

    if( $this->filters )
      foreach($this->filters as $filter)
        $this->criteria = $filter->apply($this->criteria);

    $config = array('criteria' => $this->criteria);
    if( !$this->pagination )
      $config['pagination'] = false;

    $this->products = new FActiveDataProvider('Product', $config);
    $this->products->attachEventHandler('onAfterFetchData', array($this, 'afterFetchData'));

    return $this->products;
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
}