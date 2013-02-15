<?php
/**
 * User: Nikita Melnikov <melnikov@shogo.ru>
 * Date: 12/3/12
 */
class Redirect extends FActiveRecord implements Serializable
{
  public function tableName()
  {
    return '{{seo_redirect}}';
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
    );
  }

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * String representation of object
   * @link http://php.net/manual/en/serializable.serialize.php
   * @return string the string representation of the object or null
   */
  public function serialize()
  {
    // TODO: Implement serialize() method.
  }

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Constructs the object
   * @link http://php.net/manual/en/serializable.unserialize.php
   *
   * @param string $serialized <p>
   *                           The string representation of the object.
   * </p>
   *
   * @return mixed the original value unserialized.
   */
  public function unserialize($serialized)
  {
    // TODO: Implement unserialize() method.
}}