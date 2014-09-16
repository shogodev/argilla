<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.form.models
 *
 * @method static BCallback model(string $class = __CLASS__)
 *
 * @property $name
 * @property $phone
 * @property $time
 * @property $content
 * @property $date_create
 */
class BCallback extends BActiveRecord
{
  public function behaviors()
  {
    return array(
      'dateFilterBehavior' => array(
        'class' => 'DateFilterBehavior',
        'attribute' => 'date',
      )
    );
  }

  public function rules()
  {
    return array(
      array('name, phone', 'required'),
      array('time, content, result', 'safe'),
    );
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'order' => $alias.'.date DESC',
    );
  }

  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'name'        => 'Имя',
      'phone'       => 'Телефон',
      'time'        => 'Удобное время',
      'content'     => 'Комментарий клиента',
      'result'      => 'Результат звонка',
      'date_create' => 'Дата создания'
    ));
  }

  /**
   * @param CDbCriteria $criteria
   *
   * @return CDbCriteria
   */
  protected function getSearchCriteria(CDbCriteria $criteria)
  {
    $criteria->compare('phone', $this->phone, true);
    $criteria->compare('name', $this->name, true);

    return $criteria;
  }
}