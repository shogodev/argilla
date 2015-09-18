<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ModelNotCreateException extends CException
{
  public function __construct($modelName)
  {
    $this->code = 500;
    $this->message = 'Ошибка! Не удалось создеть модель '.$modelName;
  }
}