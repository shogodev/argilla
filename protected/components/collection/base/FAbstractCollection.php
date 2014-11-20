<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.collection
 * @property FCollection $root
 */
class FAbstractCollection extends CComponent implements IteratorAggregate, ArrayAccess, Countable, JsonSerializable
{
  /**
   * @var FCollectionElement[]
   */
  protected $collectionTree = array();

  /**
   * @var FCollectionElement[]
   */
  protected $collectionList = array();

  protected $index = 0;

  /**
   * @var FAbstractCollection
   */
  protected $rootCollection;

  public function getIterator()
  {
    return new FArrayCollectionIterator($this->collectionTree);
  }

  public function removeAll()
  {
    $this->rootCollection->index = 0;
    $this->rootCollection->collectionList = array();
    $this->collectionList = array();
    $this->collectionTree = array();
  }

  public function offsetExists($offset)
  {
    return isset($this->collectionTree[$offset]) || $this->getOffsetByKey($offset) !== null;
  }

  public function offsetGet($offset)
  {
    if( !$this->offsetExists($offset) )
      return null;

    $element = isset($this->collectionTree[$offset]) ? $this->collectionTree[$offset] : $this->collectionTree[$this->getOffsetByKey($offset)];

    return $element instanceof FCollectionElement ? $element->getObject() : $element;
  }

  public function offsetSet($offset, $value)
  {
    if (is_null($offset))
    {
      $this->collectionTree[] = $value;
    }
    else
    {
      $this->collectionTree[$offset] = $value;
    }
  }

  /**
   * Проверяет существование элемента с индексом $index во всех дочерних коллекциях
   * @param $index
   *
   * @return bool
   */
  public function exists($index)
  {
    return isset($this->collectionList[$index]);
  }

  public function offsetUnset($offset)
  {
    $element = $this->rootCollection->collectionList[$offset];
    unset($element->collectionParent->collectionTree[$offset]);
    unset($this->rootCollection->collectionList[$offset]);
    unset($element);
  }

  public function count()
  {
    return $this->getIterator()->count();
  }

  public function jsonSerialize()
  {
    $data = array();

    $st = 0;
    foreach($this->collectionTree as $item)
    {
      $data[$item->key ? $item->key : $st++] = $item;
    }

    return $data;
  }

  /**
   * @return FAbstractCollection
   */
  protected function getRoot()
  {
    return $this->rootCollection;
  }

  protected function attach(FCollectionElement $element)
  {
    $element->index = $this->rootCollection->index++;
    $element->collectionParent = $this;
    $this->rootCollection->collectionList[$element->index] = $element;
    $this->collectionTree[$element->index] = $element;

    return $element->index;
  }

  protected function getOffsetByKey($key)
  {
    foreach($this->collectionTree as $element)
    {
      if( $element->key === $key )
      {
        return $element->index;
      }
    }

    return null;
  }
}