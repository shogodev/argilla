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
    if( $this->isNotEmptyCollection($index) )
      return CHtml::listData($this->collectionItems[$index], $key, $value);

    return array();
  }

  /**
   * Если collectionItems c индексом $index не пустой, то венет true
   * @param $index
   * @return bool
   */
  public function isNotEmptyCollectionItems($index)
  {
    if( isset($this->owner->collectionItems[$index]) && !empty($this->owner->collectionItems[$index]) )
      return true;
  }

  /**
   * Если существует не пустой collectionItems с индексом $index, то венет true
   * @param $index
   * @return bool
   */
  public function isNotEmptyCollection($index)
  {
    if( !$this->isNotEmptyCollectionItems($index) )
      return false;

    return $this->owner->collectionItems[$index] instanceof FCollection && !$this->owner->collectionItems[$index]->isEmpty();
  }

  public function getCollectionItems($index, $onlyCollection = false)
  {
    if( $onlyCollection )
    {
      return $this->isNotEmptyCollection($index) ? $this->owner->collectionItems[$index] : null;
    }
    else
    {
      return $this->isNotEmptyCollectionItems($index) ? $this->owner->collectionItems[$index] : null;
    }
  }

  /**
   * Метод вызывается после создания обекта колекции
   */
  public function afterCreateCollection()
  {

  }

  /**
   * Копирует арибуты поведения FCollectionElement из $object
   * @param $object
   * @return $this
   */
  public function mergeCollectionAttributes($object)
  {
    foreach(get_object_vars($this) as $attribute => $value)
      $this->owner->{$attribute} = $object->{$attribute};

    return $this->owner;
  }
}