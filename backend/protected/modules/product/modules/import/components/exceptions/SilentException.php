<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
class SilentException extends CException
{
  public function __construct($message = "", $code = 200, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }
}