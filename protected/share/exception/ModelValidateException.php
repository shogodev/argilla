<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ModelValidateException extends CException
{
  public function __construct(CModel $model, $message = '')
  {
    $fullMessage = empty($message) ? '' : $message.' .';
    $fullMessage .= 'Ошибки валидации: '.print_r($model->errors, true);
    //$message .= PHP_EOL.print_r($model->attributes, true);

    parent::__construct($fullMessage, 500);
  }
}