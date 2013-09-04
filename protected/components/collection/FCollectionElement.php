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
      'amount' => $this->owner->collectionAmount,
      'index' => $this->owner->collectionIndex,
      'items' => !empty($this->owner->collectionItems) ? $this->parentCollection->toArray($this->owner->collectionItems) : array()
    );
  }

  /**
   * @param $index
   * @param $key
   * @param $value
   *
   * @return array
   */
  public function getCollectionValues($index, $key, $value)
  {
    $values = array();

    if( isset($this->collectionItems[$index]) )
    {
      $values = Chtml::listData($this->collectionItems[$index], $key, $value);
    }

    return $values;
  }
}