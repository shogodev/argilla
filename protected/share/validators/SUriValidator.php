<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.share.validators
 */
class SUriValidator extends CRegularExpressionValidator
{
  public $pattern = '/^[\w\s]+$/';

  protected function validateAttribute($object, $attribute)
  {
    $this->message = 'Поле «'.ucfirst($attribute).'» имеет неверный формат.';
    parent::validateAttribute($object, $attribute);
  }
}