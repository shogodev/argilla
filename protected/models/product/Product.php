<?php
/**
 * @method static Product model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $parent
 * @property integer $position
 * @property string $url
 * @property string $name
 * @property string $articul
 * @property string $price
 * @property string $currency_id
 * @property string $price_old
 * @property string $notice
 * @property string $content
 * @property string $parentsName
 *
 * @property integer $visible
 * @property integer $spec
 * @property integer $novelty
 * @property integer $discount
 * @property integer $main
 * @property integer $dump
 * @property integer $archive
 * @property integer $xml
 *
 * @property ProductSection $section
 * @property ProductType $type
 * @property BProductCategory $category
 * @property ProductParameterName[] $parameters
 *
 * collectionElement behavior
 * @mixin FCollectionElement
 * @property integer $collectionIndex
 * @property integer $collectionAmount
 * @property integer $collectionItems
 */
class Product extends FActiveRecord
{
  /**
   * @var Product[]
   */
  protected $relatedProduct;

  /**
   * @var ProductParameterName[]
   */
  protected $parameters;

  protected $images;

  public function behaviors()
  {
    return array('collectionElement' => array('class' => 'FCollectionElement'));
  }

  public function relations()
  {
    return array(
      'assignment' => array(self::HAS_ONE, 'ProductAssignment', 'product_id'),
      'section' => array(self::HAS_ONE, 'ProductSection', array('section_id' => 'id'), 'through' => 'assignment'),
      'type' => array(self::HAS_ONE, 'ProductType', array('type_id' => 'id'), 'through' => 'assignment'),
      'category' => array(self::HAS_ONE, 'ProductCategory', array('category_id' => 'id'), 'through' => 'assignment'),
    );
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
      'order' => $alias.'.position'
    );
  }

  public function scopes()
  {
    return array(
      'visible' => array(
        'condition' => 'visible=1',
        'order' => 'position'
      ),
      'main' => array(
        'condition' => 'main=1'
      )
    );
  }

  public function afterFind()
  {
    if( isset(Yii::app()->controller) )
      $this->url = Yii::app()->controller->createUrl('product/one', array('url' => $this->url));

    $this->price = floatval($this->price);
    $this->price_old = floatval($this->price_old);

    if( !Yii::app()->user->isGuest )
    {
      if( empty($this->price_old) && !empty(Yii::app()->user->discount) )
      {
        $this->discount  = Yii::app()->user->discount;
        $this->price_old = $this->price;
        $this->price     = $this->price - ($this->price * $this->discount / 100);
      }
    }

    parent::afterFind();
  }

  /**
   * @param null $key
   * @param CDbCriteria $groupCriteria критерия группы параметров
   * @param CDbCriteria $criteria критерия параметров
   *
   * @return ProductParameterName[]
   */
  public function getParameters($key = null, CDbCriteria $groupCriteria = null, CDbCriteria $criteria = null)
  {
    if( !isset($this->parameters) )
    {
      $productParamNames = ProductParameterName::model();
      if( !is_null($groupCriteria) )
        $productParamNames->setGroupCriteria($groupCriteria);

      $productParamNames->addAssignmentCondition(array('section_id' => $this->section->id));

      if( $criteria === null )
        $criteria = new CDbCriteria();

      $criteria->compare('t.product', '1');
      $this->parameters = $productParamNames->search($criteria);

      foreach($this->parameters as $parameter)
        $parameter->setProductId($this->id);

      ProductParameter::model()->setParameterValues($this->parameters);
    }

    return isset($key) ? Arr::filter($this->parameters, array('groupKey', 'key'), $key) : $this->parameters;
  }

  /**
   * @param array $parameters
   *
   * @return $this
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
    return $this;
  }

  /**
   * @param $parameter
   */
  public function addParameter($parameter)
  {
    $this->parameters[] = $parameter;
  }

  /**
   * @param string $type
   *
   * @return ProductImage
   */
  public function getImage($type = 'main')
  {
    return ProductImage::model()->findByAttributes(
      array(
        'type' => $type,
        'parent' => $this->id,
      ),
      array(
        'order' => 'IF(position, position, 999999999)'
      ));
  }

  /**
   * @param string $type
   *
   * @return ProductImage[]
   */
  public function getImages($type = 'main')
  {
    if( empty($this->images) )
    {
      $images = ProductImage::model()->findAllByAttributes(
        array('parent' => $this->id),
        array('order' => 'IF(position, position, 999999999)')
      );

      $this->setImages($images, $type);
    }

    return isset($this->images[$type]) ? $this->images[$type] : array();
  }

  /**
   * @param array  $images
   * @param string $type
   *
   * @return void
   */
  public function setImages($images, $type)
  {
    if( !isset($this->images[$type]) )
      $this->images[$type] = array();

    foreach($images as $image)
      $this->images[$image['type']][] = $image;
  }

  /**
   * @return Product[]
   */
  public function getRelatedProducts()
  {
    if( $this->relatedProduct === null )
      $this->relatedProduct = $this->findAllThroughAssociation(new Product(), false);

    return $this->relatedProduct;
  }

  /**
   * @return ProductParameterName[]
   */
  public function getProductOneParameters()
  {
    return $this->getParametersByAttributes(array('product' => 1));
  }

  /**
   * @return ProductParameterName[]
   */
  public function getProductLineParameters()
  {
    return $this->getParametersByAttributes(array('section_list' => 1));
  }

  /**
   * @return ProductParameterName[]
   */
  public function getProductTabletParameters()
  {
    return $this->getParametersByAttributes(array('section' => 1));
  }

  private function getParametersByAttributes(array $attributes, $notEmptyValue = true, $exceptionKeys = array())
  {
    $parameters = array();

    foreach($this->getParameters() as $parameter)
    {
      if( $notEmptyValue && empty($parameter->value) )
        continue;

      if( in_array($parameter->key, $exceptionKeys) )
        continue;

      foreach($attributes as $attribute => $value)
      {
        if( isset($parameter->{$attribute}) && $parameter->{$attribute} == $value )
          $parameters[] = $parameter;
      }
    }

    return $parameters;
  }
}