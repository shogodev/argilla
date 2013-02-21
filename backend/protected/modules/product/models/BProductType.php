<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 *
 * @method static BProductType model(string $class = __CLASS__)
 *
 * @property string  $id
 * @property integer $position
 * @property string  $url
 * @property string  $name
 * @property string  $notice
 * @property integer $visible
 */
class BProductType extends BActiveRecord
{
  public $section_id;

  public function rules()
  {
    return array(
      array('url', 'unique'),
      array('url, name', 'required'),
      array('position, visible', 'numerical', 'integerOnly' => true),
      array('url, name', 'length', 'max' => 255),
      array('notice', 'safe'),
      array('section_id', 'safe', 'on' => 'search'),
    );
  }

  public function relations()
  {
    return array(
      'treeAssignment' => array(self::HAS_MANY, 'BProductTreeAssignment', 'src_id', 'on' => 'src="type"'),
      'section' => array(self::HAS_ONE, 'BProductSection', 'dst_id', 'on' => 'dst="section"', 'through' => 'treeAssignment'),
    );
  }

  public function afterDelete()
  {
    BProductTreeAssignment::assignToModel($this, 'section')->delete();
    return parent::afterDelete();
  }

  public function attributeLabels()
  {
    return Cmap::mergeArray(parent::attributeLabels(), array(
      'section_id' => 'Раздел',
    ));
  }

  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->together = true;
    $criteria->with = array('section');

    $criteria->compare('section.id', '='.$this->section_id);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }
}