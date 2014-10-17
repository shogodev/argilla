<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.validators
 *
 * Example:
 *
 * array('payer_name', 'ExRequiredValidator', 'dependedAttribute' => 'payment_id', 'dependedValue' => OrderPaymentType::BANK_PAYMENT)
 * array('payer_name', 'ExRequiredValidator', 'dependedAttribute' => 'payment_id', 'dependedValue' => array(1, 3, 4), 'not' => true)
 */
class ExRequiredValidator extends CRequiredValidator
{
  public $dependedAttribute;

  public $dependedValue;

  public $not = false;

  public function validateAttribute($object, $attribute)
  {
    $equal = false;

    if( is_array($this->dependedValue) )
    {
      $equal = in_array($object->{$this->dependedAttribute}, $this->dependedValue);
    }
    else
    {
      $equal = $object->{$this->dependedAttribute} == $this->dependedValue;
    }

    if( !$this->not ? $equal : !$equal  )
    {
      parent::validateAttribute($object, $attribute);
    }
  }
}