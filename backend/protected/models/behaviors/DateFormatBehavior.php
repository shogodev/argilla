<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.controllers.behaviors
 *
 * @property string $attr
 */
class DateFormatBehavior extends CActiveRecordBehavior
{
  public $attribute;

  public $defaultNow = false;

  public function attach($owner)
  {
    parent::attach($owner);

    if( empty($this->attribute) )
      throw new CHttpException(500, "Ошибка. Не указано свойство attribute для поведения ".get_class($this));
  }

  /**
   * @param CEvent $event
   * @throws CHttpException
   */
  public function afterFind($event)
  {
    if( !$this->owner->hasAttribute($this->attribute) && property_exists($this->owner, $this->attribute) )
      throw new CHttpException(500, "Ошибка. Свойство $this->attribute не найдено в классе ".get_class($this->owner));

    $this->attr = !$this->isEmptyDate($this->attr) ? $this->sqlDateToDate($this->attr) : '';
  }

  /**
   * @param CEvent $event
   */
  public function beforeSave($event)
  {
    if( $this->defaultNow )
      $this->attr = $this->isEmptyDate($this->attr) ? date('Y-m-d') : $this->dateToSqlDate($this->attr);
    else
      $this->attr = $this->dateToSqlDate($this->attr);
  }

  protected function isEmptyDate($value)
  {
    if( empty($value) )
      return true;

    if( $value == '0000-00-00')
      return true;

    return false;
  }

  protected function getAttr()
  {
    return $this->owner->{$this->attribute};
  }

  protected function setAttr($value)
  {
    $this->owner->{$this->attribute} = $value;
  }

  protected function dateToSqlDate($value)
  {
    return !empty($value) ? date('Y-m-d', strtotime($value)) : $value;
  }

  protected function sqlDateToDate($value)
  {
    return date('d.m.Y', strtotime($value));
  }
}