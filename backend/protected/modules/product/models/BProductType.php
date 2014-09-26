<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product.models
 *
 * @method static BProductType model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $position
 * @property string  $url
 * @property string  $name
 * @property string  $notice
 * @property integer $visible
 *
 * @mixin BTreeAssignmentBehavior
 */
class BProductType extends BProductStructure
{
  public function rules()
  {
    return array(
      array('url', 'unique'),
      array('url, name', 'required'),
      array('position, visible', 'numerical', 'integerOnly' => true),
      array('url, name', 'length', 'max' => 255),
      array('notice', 'safe'),
    );
  }

  public function behaviors()
  {
    return array(
      'tree' => array('class' => 'BTreeAssignmentBehavior', 'parentModel' => 'BProductSection'),
      'uploadBehavior' => array('class' => 'UploadBehavior', 'validAttributes' => 'img'),
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'parent_id' => 'Раздел',
    ));
  }

  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('t.visible', $this->visible);
    $criteria->compare('t.name', $this->name, true);

    return $criteria;
  }
}