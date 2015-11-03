<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ImportModelValidateException extends WarningException
{
  public function __construct(CModel $model, $message = '')
  {
    $fullMessage = empty($message) ? '' : $message.' .';
    $fullMessage .= 'Ошибки валидации: '.$this->clearArrayData(print_r($model->errors, true));
    //$message .= PHP_EOL.print_r($model->attributes, true);

    parent::__construct($fullMessage, 500);
  }

  private function clearArrayData($string)
  {
    $string = preg_replace('/\s+/', ' ', $string);
    $string = preg_replace('/\n?Array\s\(\s|\)/', '', $string);
    $string = preg_replace('/\s=>\s\[\d+\]/', '', $string);
    $string = preg_replace('/(\[\w+\])/', "\n\r$1", $string);
    $string = preg_replace('/\ +/', ' ', $string);
    return $string;
  }
}