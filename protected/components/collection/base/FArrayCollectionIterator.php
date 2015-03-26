<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.collection
 */
class FArrayCollectionIterator extends ArrayIterator
{
  public function key()
  {
    /**
     * @var FCollectionElement $element
     */
    $element = $this[parent::key()];

    return $element instanceof FCollectionElement && $element->key ? $element->key : parent::key();
  }

  public function current()
  {
    /**
     * @var FCollectionElement $element
     */
    $element = $this[parent::key()];

    return $element instanceof FCollectionElement ? $element->getObject() : parent::current();
  }
}