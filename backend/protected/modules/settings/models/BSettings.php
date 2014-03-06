<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.settings.models
 *
 * @property string $param
 * @property string $value
 * @property string $notice
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

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'param' => 'Параметр',
      'value' => 'Значение',
      'notice' => 'Описание',
    ));
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('param', $this->param, true);
    $criteria->compare('value', $this->value, true);

    return $criteria;
  }
}