<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.common
 */
class FAssociationCollection implements Countable, IteratorAggregate
{
  /**
   * @var FActiveRecord
   */
  protected $owner = null;

  /**
   * @var FActiveRecord[]
   */
  protected $related = array();

  /**
   * @param FActiveRecord $owner
   * @param FActiveRecord $related
   */
  public function __construct(FActiveRecord $owner, FActiveRecord $related)
  {
    $this->owner = $owner;
    $this->related = $this->owner->findAllThroughAssociation($related);
  }

  /**
   * @return bool
   */
  public function hasRelated()
  {
    return !empty($this->related);
  }

  /**
   * @return FActiveRecord[]
   */
  public function getRelated()
  {
    return $this->related;
  }

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Count elements of an object
   * @link http://php.net/manual/en/countable.count.php
   * @return int The custom count as an integer.
   * </p>
   * <p>
   * The return value is cast to an integer.
   */
  public function count()
  {
    return count($this->related);
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Retrieve an external iterator
   * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
   * @return Traversable An instance of an object implementing <b>Iterator</b> or
   * <b>Traversable</b>
   */
  public function getIterator()
  {
    return new ArrayIterator($this->related);
  }
}