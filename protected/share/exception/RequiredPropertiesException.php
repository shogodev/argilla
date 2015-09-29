<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Пример:
 * throw new RequiredPropertiesException(__CLASS__, 'submitCssClass')
 * или
 * throw new RequiredPropertiesException(__CLASS__, array('submitCssClass', 'name'))
 */
/**
 * Class RequiredPropertiesException
 * Исключение неустановленных обязательных свойсте
 */
class RequiredPropertiesException extends CException
{
  public function __construct($className, $properties)
  {
    $this->code = 500;

    $properties = is_array($properties) ? $properties : array($properties);
    $this->message = 'Ошибка! Не '.(count($properties) == 1 ? 'заданно свойство '.reset($properties) : 'заданны свойства '.implode(', ', $properties)).' класса '.$className;
  }
}