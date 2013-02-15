<?php
/**
 * @property string $id
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
 * @property ParamName[] parameters

 * @property ProductConfiguration configuration
 */
class Product extends FActiveRecord
{
  public $collectionIndex;
  public $count;
  public $sum;

  protected $parameters;
  protected $images;

  /**
   * @var array
   */
  protected $technologies = array();

  /**
   * @OVERRIDE
   *
   * @return string
   */
  public function tableName()
  {
    return '{{product}}';
  }

  /**
   * @OVERRIDE
   *
   * @return array
   */
  public function relations()
  {
    return array(
      'assignment' => array(self::HAS_ONE, 'ProductAssignment', 'product_id'),

      'section'       => array(self::HAS_ONE, 'ProductSection', array('section_id' => 'id'), 'through' => 'assignment'),
      'type'          => array(self::HAS_ONE, 'ProductType', array('type_id' => 'id'), 'through' => 'assignment'),
      'configuration' => array(self::HAS_ONE, 'ProductConfiguration', array('configuration_id' => 'id'), 'through' => 'assignment'),
    );
  }

  /**
   * @OVERRIDE
   *
   * @return array
   */
  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
      'order' => $alias.'.position'
    );
  }

  /**
   * @OVERRIDE
   *
   * @return array
   */
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

  /**
   * @OVERRIDE
   *
   * @return void
   */
  public function afterFind()
  {
    $this->url       = Yii::app()->controller->createUrl('product/one', array('url' => $this->url));

    $this->price     = floatval($this->price);
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

  public function getParameters()
  {
    if( empty($this->parameters) )
    {
      $paramModel = ProductParam::model();
      $paramModel->product_id = $this->id;

      $criteria = new CDbCriteria();
      $criteria->compare('product', '=1');

      $this->parameters = $paramModel->getParameters($criteria);

      foreach($this->parameters as $i => $param)
        if( is_array($param->value) )
          $this->parameters[$i]->value = $param->setVariants($param->variants);
    }

    return $this->parameters;
  }

  /**
   * @param array $parameters
   *
   * @return void
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
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

  public function getTechnologies()
  {
    if( empty($this->technologies) )
      $this->technologies = $this->findAllThroughAssociation(new Info());

    return $this->technologies;
  }

  public function getParentsName()
  {
    $name = array();

    if( !empty($this->section) )
      $name[] = $this->section->name;

    if( !empty($this->type) )
      $name[] = $this->type->name;

    return $name ? implode(" : ", $name) : "";
  }
}