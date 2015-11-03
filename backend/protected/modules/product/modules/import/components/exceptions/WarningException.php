<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class WarningException extends CException
{
  public function __construct($message = "", $code = 0, Exception $previous = null)
  {
    parent::__construct($message, 200, null);
  }
}