<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package
 */
class Showcase extends CComponent implements IteratorAggregate, Countable, ArrayAccess
{
  /**
   * @var CDbCriteria
   */
  private $criteria;

  /**
   * @var array
   */
  private $tabs = array();

  public function __construct(CDbCriteria $defaultCriteria)
  {
    $this->criteria = $defaultCriteria;
    $this->criteria->compare('t.visible', 1);
  }

  /**
   * @param $name
   * @param $column
   * @param $value
   */
  public function createTabByCondition($name, $column, $value)
  {
    $criteria = new CDbCriteria();
    $criteria->compare($column, $value);

    $this->createTabByCriteria($name, $criteria);
  }

  /**
   * @param $name
   * @param CDbCriteria $criteria
   */
  public function createTabByCriteria($name, CDbCriteria $criteria)
  {
    $criteria->mergeWith($this->criteria);

    $productList = new ProductList($criteria, null, false);
    $dataProvider = $productList->getDataProvider();

    if( $dataProvider && $dataProvider->totalItemCount )
    {
      $this->tabs[$name] = new ShowcaseTab($name, $dataProvider, $this->count());
    }
  }

  /**
   * @return ArrayIterator|Traversable
   */
  public function getIterator()
  {
    return new ArrayIterator($this->tabs);
  }

  /**
   * @return int
   */
  public function count()
  {
    return count($this->tabs);
  }

  /**
   * @param mixed $offset
   *
   * @return bool
   */
  public function offsetExists($offset)
  {
    return isset($this->tabs[$offset]);
  }

  /**
   * @param mixed $offset
   *
   * @return mixed|null
   */
  public function offsetGet($offset)
  {
    return $this->offsetExists($offset) ? $this->tabs[$offset] : null;
  }

  /**
   * @param mixed $offset
   * @param mixed $value
   */
  public function offsetSet($offset, $value)
  {
    if( is_null($offset) )
      $this->tabs[] = $value;
    else
      $this->tabs[$offset] = $value;
  }

  /**
   * @param mixed $offset
   */
  public function offsetUnset($offset)
  {
    unset($this->tabs[$offset]);
  }
}