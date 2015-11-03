<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.models
 */

Yii::import('backend.modules.product.models.behaviors.*');

/**
 * @method static BProduct model(string $class = __CLASS__)
 *
 * @property string $id
 * @property integer $parent
 * @property integer $position
 * @property string $url
 * @property string $name
 * @property string $articul
 * @property string $price
 * @property string $price_old
 * @property string $notice
 * @property string $content
 *
 * @property integer $gift
 * @property integer $visible
 * @property integer $spec
 * @property integer $novelty
 * @property integer $discount
 * @property integer $main
 * @property integer $dump
 * @property integer $archive
 * @property integer $xml

 * @property integer $section_id
 * @property integer $type_id
 * @property integer $category_id
 * @property integer $collection_id
 *
 * @property BProductAssignment $assignment
 * @property BAssociation[] $associations
 */
class BProduct extends BActiveRecord
{
  public function __get($name)
  {
    $fields = BProductAssignment::model()->getFields();

    if( isset($fields[$name]) )
    {
      $relation = str_replace('_id', '', $name);

      if( is_array($this->$relation) )
        $value = CHtml::listData($this->$relation, 'id', 'id');
      else if( isset($this->$relation->id) )
        $value = $this->$relation->id;
      else
        $value = null;
    }
    else
    {
      $value = parent::__get($name);
    }

    return $value;
  }

  public function __set($name, $value)
  {
    $fields = BProductAssignment::model()->getFields();

    if( isset($fields[$name]) )
      $this->$name = $value;
    else
      parent::__set($name, $value);
  }

  public function __isset($name)
  {
    $fields = BProductAssignment::model()->getFields();

    if( isset($fields[$name]) )
      return true;
    else
      return parent::__isset($name);
  }

  public function rules()
  {
    return array(
      array('url, name, articul', 'required'),
      array('url, articul', 'unique'),
      array('parent, position, visible, spec, novelty, main, dump, discount, archive, xml', 'numerical', 'integerOnly' => true),
      array('url, name, articul', 'length', 'max' => 255),
      array('notice, content, video, rating', 'safe'),
      array('price, price_old', 'numerical'),

      array('price_old', 'filter', 'filter' => function($value) {
        if( PriceHelper::isEmpty($value) )
          return null;
        return $value;
      }),
      array('price_old', 'compare', 'allowEmpty' => true, 'compareAttribute' => 'price', 'operator' => '>'),

      array('url', 'SUriValidator'),
      array('name, url, articul', 'filter', 'filter' => array(Yii::app()->format, 'trim')),
      array('url', 'filter', 'filter' => array(Yii::app()->format, 'toLower')),

      array('section_id', 'required', 'except' => BModificationBehavior::SCENARIO_MODIFICATION),
      array(implode(", ", array_keys(BProductAssignment::model()->getFields())), 'safe'),
    );
  }

  public function behaviors()
  {
    return array(
      'uploadBehavior' => array(
        'class' => 'UploadBehavior',
        'validAttributes' => 'product_img'
      ),
      'facetedSearchBehavior' => array(
        'class' => 'BFacetedSearchBehavior'
      ),
      'associatedFilter' => array(
        'class' => 'AssociatedFilterBehavior',
      ),
    );
  }

  public function relations()
  {
    return array(
      'assignment' => array(self::HAS_MANY, 'BProductAssignment', 'product_id'),
      'associations' => array(self::HAS_MANY, 'BAssociation', 'src_id', 'on' => 'src="bproduct"'),
      'products' => array(self::HAS_MANY, 'BProduct', 'dst_id', 'on' => 'dst="product"', 'through' => 'associations'),
      'section' => array(self::HAS_ONE, 'BProductSection', 'section_id', 'through' => 'assignment'),
      'category' => array(self::HAS_ONE, 'BProductCategory', 'category_id', 'through' => 'assignment'),
      'collection' => array(self::HAS_ONE, 'BProductCollection', 'collection_id', 'through' => 'assignment'),
      'type' => array(self::HAS_ONE, 'BProductType', 'type_id', 'through' => 'assignment'),
    );
  }

  public function beforeSave()
  {
    if( parent::beforeSave() )
    {
      if( empty($this->articul) )
        $this->articul = null;

      return true;
    }

    return false;
  }

  public function getImageTypes()
  {
    return array(
      'main' => 'Основное',
      'gallery' => 'Галерея'
    );
  }

  public function getParameterVariants($key)
  {
    $variants  = array();
    $parameter = BProductParamName::model()->findByAttributes(array('key' => $key));

    if( $parameter )
      $variants = $parameter->variants;

    return $variants;
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'product_img' => 'Изображения',
      'BProduct' => 'Продукты',
      'spec' => 'Лидер продаж',
      'price_raw' => 'Цена',
    ));
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->together = true;
    $criteria->distinct = true;

    $criteria->with = array('assignment' => [
      'select' => false,
    ]);

    foreach($this->getFilteredGridSettings() as $filteredGridSetting)
    {
      if( property_exists($this, $filteredGridSetting->name) || isset($this->getMetaData()->columns[$filteredGridSetting->name]) )
      {
        if( $filteredGridSetting->filter == BGridSettings::FILTER_COMPARE )
        {
          $criteria->compare('t.'.$filteredGridSetting->name, trim($this->{$filteredGridSetting->name}));
        }
        else if( $filteredGridSetting->filter == BGridSettings::FILTER_CONTAIN )
        {
          $criteria->compare('t.'.$filteredGridSetting->name, trim($this->{$filteredGridSetting->name}), true);
        }
      }
    }

    foreach(BProductAssignment::model()->getFields() as $key => $field)
      if( !is_array($this->$key) )
        $criteria->compare('assignment.'.$key, $this->$key);

    return $criteria;
  }

  protected function getSearchParams()
  {
    return array('sort' => array('defaultOrder' => 't.id ASC'));
  }

  /**
   * @return BGridSettings[]
   */
  protected function getFilteredGridSettings()
  {
    Yii::import('backend.modules.settings.models.BGridSettings');

    return BGridSettings::model()->visible()->filter()->findAll();
  }
}