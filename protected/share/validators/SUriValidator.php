<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class SUriValidator extends CValidator
{
  /**
   * Validates a single attribute.
   * This method should be overridden by child classes.
   *
   * @param CModel $object the data object being validated
   * @param string $attribute the name of the attribute to be validated.
   */
  protected function validateAttribute($object, $attribute)
  {
    if( !preg_match('/^(\w+[-]*(\w+)*)$/i', $object->{$attribute}) )
      $this->addError($object, $attribute, 'Неверный формат uri');
  }
}