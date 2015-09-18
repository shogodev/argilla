<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class Http404Exception extends CHttpException
{
  public function __construct($status = 404, $message = 'Страница не найдена',$code = 0)
  {
    parent::__construct($status, $message, $code);
  }
}