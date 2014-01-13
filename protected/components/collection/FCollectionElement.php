<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components
 *
 * collectionElement behavior
 * @property integer $collectionIndex
 * @property integer $collectionAmount
 * @property integer $collectionItems
 * @property integer $sum
 * @method string removeButton(string $text = '', array $htmlOption = array())
 * @method string amountInput(array $htmlOptions = array() )
 */
class FCollectionElement extends CBehavior
{
  public $collectionIndex;

  public $collectionAmount;

  public $collectionItems;

  /**
   * @var FCollection $parentCollection
   */
  public $parentCollection;

  public $collectionPath = array();

  public $collectionExternalIndex;

  public function getSum()
  {
    return $this->owner->price * $this->collectionAmount;
  }

  public function indexationElement($path, $rootCollection)
  {
    $this->owner->collectionPath = $path;
    $this->owner->collectionExternalIndex = $this->owner->parentCollection->pathToString($path);
    $rootCollection->externalIndexStorage[$this->owner->collectionExternalIndex] = $this->owner;
  }

  public function toArray()
  {
    return array(
      'id' => $this->owner->primaryKey,
      'type' => Utils::toSnakeCase(get_class($this->owner)),
      'amount' => $this->collectionAmount,
      'index' => $this->collectionIndex,
      'items' => $this->collectionItemsToArray()
    );
  }

  public function collectionItemsToArray()
  {
    if( empty($this->collectionItems) )
      return array();

    return $this->parentCollection instanceof FCollection ? $this->parentCollection->toArray($this->collectionItems) : $this->collectionItems;
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
    $values = array();

    if( !$this->isEmptyCollectionItems($index) )
      $values = CHtml::listData($this->collectionItems[$index], $key, $value);

    return $values;
  }

  public function isEmptyCollectionItems($index)
  {
    return !isset($this->owner->collectionItems[$index]) || !($this->owner->collectionItems[$index] instanceof FCollection) || $this->owner->collectionItems[$index]->isEmpty();
  }

  /**
   * Метод вызывается после создания обекта колекции
   */
  public function afterCreateCollection()
  {

  }
}