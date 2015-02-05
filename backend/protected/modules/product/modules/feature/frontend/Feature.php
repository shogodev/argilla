<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @method static Feature model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $position
 * @property string $image
 * @property string $name
 * @property string $notice
 *
 * @property Association[] $associations
 */
class Feature extends FActiveRecord
{
  public function tableName()
  {
    return '{{product_feature}}';
  }

  public function behaviors()
  {
    return array(
      'imageBehavior' => array('class' => 'SingleImageBehavior', 'path' => 'product/feature'),
    );
  }

  public function relations()
  {
    return array(
      'associations' => array(self::HAS_MANY, 'Association', 'dst_id', 'on' => 'dst_frontend="Feature"')
    );
  }

  public function defaultScope()
  {
    return array(
      'order' => "IF(position=0, 999999999, position), id",
    );
  }

  public function getValue()
  {
    return $this->name.(!empty($this->notice) ? ': '.$this->notice : '');
  }
}