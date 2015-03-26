<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.models.behaviors
 *
 * Поведение преобразует формат даты.<br/>
 * После чтения преобразует из YYYY-MM-DD в DD.MM.YYY<br/>
 * Перед записью преобразует DD.MM.YYY в YYYY-MM-DD
 *
 * $attribute - атрибут поля даты<br/>
 * $defaultNow - флаг может быть true или false, при пустом значении записывает в $attribute текущую дату. По умолчанию false
 *
 * Пример:
 * <pre>
 *  'dateFormatBehavior' => array(
 *    'class' => 'DateFormatBehavior',
 *    'attribute' => 'date',
 *    'defaultNow' => true
 *  )
 * </pre>
 *
 * @property bool $defaultNow
 */
class DateFormatBehavior extends SActiveRecordBehavior
{
  /**
   * @var string атрибут поля даты
   */
  public $attribute;

  public $validateFormat;

  /**
   * @var bool флаг может быть true или false, при пустом значении записывает в $attribute текущую дату. По умолчанию false
   */
  public $defaultNow = false;

  private $sqlDateFormat;

  private $inputFormat;

  private $dbType;

  public function init()
  {
    if( empty($this->attribute) )
      throw new CHttpException(500, "Ошибка. Не указано свойство attribute для поведения ".get_class($this));
    if( is_array($this->attribute) )
      throw new CHttpException(500, "Ошибка. Свойство attribute для поведения ".get_class($this)." не должно быть массивом");

    $column = $this->owner->tableSchema->getColumn($this->attribute);
    $this->dbType = $column->dbType;

    if( in_array($this->dbType, array('datetime', 'timestamp')) )
    {
      $this->sqlDateFormat = 'Y-m-d H:i:s';
      $this->inputFormat = 'd.m.Y H:i:s';

      if( is_null($this->validateFormat) || $this->validateFormat != false )
        $this->validateFormat = 'mm.dd.yyyy hh:mm:ss';
    }
    else if( $this->dbType == 'date' )
    {
      $this->sqlDateFormat = 'Y-m-d';
      $this->inputFormat = 'd.m.Y';

      if( is_null($this->validateFormat) || $this->validateFormat != false )
        $this->validateFormat = 'mm.dd.yyyy';
    }
    else
      throw new CHttpException(500, "Ошибка. Неизвестный формат даты ".$column->dbType);

    $this->setValidator();
  }

  /**
   * @param CEvent $event
   * @throws CHttpException
   */
  public function afterFind($event)
  {
    if( !$this->owner->hasAttribute($this->attribute) && property_exists($this->owner, $this->attribute) )
      throw new CHttpException(500, "Ошибка. Свойство $this->attribute не найдено в классе ".get_class($this->owner));

    $this->attr = $this->formatDate($this->inputFormat, $this->attr);
  }

  /**
   * @param CEvent $event
   */
  public function beforeSave($event)
  {
    $this->attr = $this->formatDate($this->sqlDateFormat, $this->attr, $this->defaultNow);
    if( empty($this->attr) && $this->dbType == 'timestamp' )
      $this->attr = null;
  }

  protected function isEmptyDate($value)
  {
    if( empty($value) )
      return true;

    if( $value == '0000-00-00')
      return true;

    if( $value == '0000-00-00 00:00:00')
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
  protected function formatDate($format, $value, $nowIsEmpty = false)
  {
    if( $this->isEmptyDate($value) )
    {
      if( $nowIsEmpty )
        return date($format);
      else
        return '';
    }

    return date($format, strtotime($value));
  }

  protected function setValidator()
  {
    if( !empty($this->validateFormat) )
    {
      $validator = new CDateValidator();
      $validator->attributes = array($this->attribute);
      $validator->format = $this->validateFormat;
      $this->owner->validatorList->add($validator);
    }
  }
}