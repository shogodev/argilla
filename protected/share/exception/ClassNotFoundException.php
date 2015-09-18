<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ClassNotFoundException extends CException
{
  public function __construct($className, $path = '')
  {
    $this->code = 500;
    $this->message = 'Ошибка! Не удалось найти класс '.$className;
    if( !empty($path))
      $this->message .= ' по пути '.$path;
  }
}