<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
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
    if( is_null($this->collectionElement) )
      return null;

    if( is_null($index) )
      return $this->collectionElement->items;
    else
    {
      return !is_null($this->collectionElement->items) && isset($this->collectionElement->items[$index]) ? $this->collectionElement->items[$index] : null;
    }
  }

  public function getSum()
  {
    try
    {
      $price = $this->owner->getPrice();
    }
    catch(Exception $e)
    {
      $price = $this->owner->price;
    }

    return $price * $this->collectionAmount;
  }

  public function getSumTotal()
  {
    $price = 0;

    $collectionItemsForSum = $this->collectionElement->collectionParent->root->collectionItemsForSum;

    if( $collectionItemsForSum == FCollection::COLLECTION_ITEMS_ROOT )
    {
      $price += $this->getCollectionItemSum();
    }
    else if( is_array($collectionItemsForSum) )
    {
      foreach($collectionItemsForSum as $key)
      {
        $price += $this->getCollectionItemSum($key);
      }
    }

    return $this->owner->getSum() + $price * $this->collectionAmount;
  }

  public function getCollectionItemSum($index = null)
  {
    $sum = 0;

    if( $items = $this->getCollectionItems($index) )
    {
      if( !($items instanceof FCollection) )
        $items = array($items);

      foreach($items as $item)
      {
        if( $item instanceof FCollection )
        {
          foreach($item as $innerItem)
            $sum += $innerItem->getSum();
        }
        else if( !empty($item) )
        {
          $sum += $item->getSum();
        }
      }
    }

    return $sum;
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

  public function toArray()
  {
    return array(
      'id' => $this->owner->primaryKey,
      'type' => Utils::toSnakeCase(get_class($this->owner))
    );
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