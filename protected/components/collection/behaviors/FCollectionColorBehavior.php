<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @package frontend.components.collection
 *
 * @property ProductColor $owner
 */
class FCollectionColorBehavior extends FCollectionElementBehavior
{
  public function getOrderItemType()
  {
    return 'color';
  }

  public function getPrimaryKey()
  {
    return $this->owner->getPrimaryKey();
  }

  public function getOrderItemName()
  {
    return 'Цвет';
  }

  public function getOrderItemValue()
  {
    return $this->owner->color->name;
  }
}