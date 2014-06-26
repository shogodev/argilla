<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @package frontend.components.collection
 *
 * @property ProductParameter $owner
 */
class FCollectionParameterBehavior extends FCollectionElementBehavior
{
  public function getOrderItemType()
  {
    return 'parameter-'.$this->owner->parameterName->id;
  }

  public function getPrimaryKey()
  {
    return $this->owner->getPrimaryKey();
  }

  public function getOrderItemName()
  {
    return $this->owner->parameterName->name;
  }

  public function getOrderItemValue()
  {
    return $this->owner->variant->name;
  }
}