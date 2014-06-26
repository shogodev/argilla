<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.collection
 *
 * @property FActiveRecord $owner
 * @property integer $sum
 * @property integer $collectionAmount
 * @property integer $collectionIndex
 * @property integer $collectionItems
 */
class FCollectionElementBehavior extends CBehavior
{
  /**
   * @var FCollectionElement
   */
  protected $collectionElement;

  /**
   * @param string|null $index
   *
   * @return FCollection|null
   */
  public function getCollectionItems($index = null)
  {
    if( is_null($index) )
      return $this->collectionElement->items;
    else
    {
      return !is_null($this->collectionElement->items) && isset($this->collectionElement->items[$index]) ? $this->collectionElement->items[$index] : null;
    }
  }

  public function getSum()
  {
    return $this->owner->price * $this->collectionAmount;
  }

  /**
   * @param $index
   * @param $key
   * @param $value
   *
   * @return array
   */
  public function collectionItemsListData($index, $key, $value)
  {
    if( $items = $this->getCollectionItems($index) )
      return CHtml::listData($items, $key, $value);

    return array();
  }

  /**
   * Метод вызывается после создания обекта колекции
   */
  public function afterCreateCollection()
  {

  }

  /**
   * Копирует арибуты поведения FCollectionElementBehavior из $object
   * @param $object
   * @return $this
   */
  public function mergeCollectionAttributes($object)
  {
    foreach(get_object_vars($this) as $attribute => $value)
      $this->owner->{$attribute} = $object->{$attribute};

    return $this->owner;
  }

  /**
   * @return array
   */
  public function defaultCollectionItems()
  {
    return array();
  }

  /**
   * Внутренние параметры заказа, не проходящие через коллекцию (те которые нальзя изменять)
   *
   * @return array|FCollectionCustomParameter[]
   */
  public function innerCollectionItems()
  {
    return array();
  }

  public function getOrderItemType()
  {
    return get_class($this->owner);
  }

  public function getPrimaryKey()
  {
    return $this->owner->getPrimaryKey();
  }

  public function getOrderItemName()
  {
    return 'Не задано';
  }

  public function getOrderItemValue()
  {
    return (string)$this->owner;
  }

  public function getOrderItemAmount()
  {
    return 0;
  }

  public function getOrderItemPrice()
  {
    return 0;
  }

  protected function getCollectionIndex()
  {
    return isset($this->collectionElement) ? $this->collectionElement->index : null;
  }

  protected function getCollectionAmount()
  {
    return isset($this->collectionElement) ? $this->collectionElement->amount : 1;
  }

  protected function setCollectionElement($link)
  {
    $this->collectionElement = $link;
  }

  protected function getCollectionElement()
  {
    return $this->collectionElement;
  }
}