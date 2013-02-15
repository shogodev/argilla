<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
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
  public $date_from;

  public $date_to;

  public function tableName()
  {
    return '{{callbacks}}';
  }

  public function rules()
  {
    return array(
      array('name, phone', 'required'),
      array('time, content, result', 'safe'),
      array('date_from, date_to', 'safe', 'on' => 'search'),
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

  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('phone', $this->phone, true);
    $criteria->compare('name', $this->name, true);

    if( !empty($this->date_from) || !empty($this->date_to) )
      $criteria->addBetweenCondition('date', Utils::dayBegin($this->date_from), Utils::dayEnd($this->date_to));

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }
}