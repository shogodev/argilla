<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.settings
 *
 */
class BSettings extends BActiveRecord
{
  public function rules()
  {
    return array(
      array('param, value', 'required'),
      array('param, value', 'length', 'max' => 255),
      array('notice', 'safe'),
    );
  }

  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('param', $this->param, true);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'param' => 'Параметр',
      'value' => 'Значение',
      'notice' => 'Описание',
    ));
  }
}