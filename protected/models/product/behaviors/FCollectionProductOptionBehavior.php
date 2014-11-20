<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @package frontend.components.collection
 *
 * @property ProductOption $owner
 */
class FCollectionProductOptionBehavior extends FCollectionElementBehavior
{
  public function getPrimaryKey()
  {
    return $this->owner->getPrimaryKey();
  }

  public function getOrderItemName()
  {
    return 'Опция';
  }

  public function getOrderItemValue()
  {
    return $this->owner->name;
  }

  public function getOrderItemPrice()
  {
    return $this->owner->price;
  }

  public function getOrderItemAmount()
  {
    return $this->owner->getCollectionAmount();
  }
}