<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */

/**
 * Class BTag
 *
 * @property integer $id
 * @property string $name
 * @property string $group
 */
class BTag extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('name', 'filter', 'filter' => array(Yii::app()->format, 'trim')),
      array('name', 'unique'),
      array('name, group', 'required'),
      array('name, group', 'length', 'max' => 255),
      array('id, name, group', 'safe', 'on' => 'search'),
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'name' => 'Тег',
      'group' => 'Группа',
    ));
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('id', $this->id);
    $criteria->compare('name', $this->name, true);
    $criteria->compare('`group`', $this->group, true);

    return $criteria;
  }
}