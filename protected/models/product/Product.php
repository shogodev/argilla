<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product
 *
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
 * @property ProductCategory $category
 * @property ProductCollection $collection
 * @property ProductParameterName[] $parameters
 *
 * @mixin FCollectionElementBehavior
 * @property integer $collectionIndex
 * @property integer $collectionAmount
 * @property integer $collectionItems
 *
 * @mixin ProductParametersBehavior
 * @mixin ActiveImageBehavior
 */
class Product extends FActiveRecord
{
  /**
   * @var Product[]
   */
  protected $relatedProduct;

  public function behaviors()
  {
    return array(
      'collectionElement' => array('class' => 'FCollectionElementBehavior'),
      'productParametersBehavior' => array('class' => 'ProductParametersBehavior'),
      'imagesBehavior' => array('class' => 'ActiveImageBehavior', 'imageClass' => 'ProductImage'),
    );
  }

  public function relations()
  {
    return array(
      'assignment' => array(self::HAS_ONE, 'ProductAssignment', 'product_id'),
      'section' => array(self::HAS_ONE, 'ProductSection', array('section_id' => 'id'), 'through' => 'assignment'),
      'type' => array(self::HAS_ONE, 'ProductType', array('type_id' => 'id'), 'through' => 'assignment'),
      'category' => array(self::HAS_ONE, 'ProductCategory', array('category_id' => 'id'), 'through' => 'assignment'),
      'collection' => array(self::HAS_ONE, 'ProductCollection', array('collection_id' => 'id'), 'through' => 'assignment'),
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

    parent::afterFind();
  }

  /**
   * @return string
   */
  public function getHeader()
  {
    return $this->name;
  }

  /**
   * @return string
   */
  public function getPrice()
  {
    return $this->price;
  }

  /**
   * @return string
   */
  public function getPriceOld()
  {
    return $this->price_old;
  }
}