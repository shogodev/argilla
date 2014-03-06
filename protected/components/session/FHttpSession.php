<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.session
 *
 * @property string $stateKeyPrefix
 */
class FHttpSession extends CHttpSession
{
  protected  $_keyPrefix;

  public function getIterator()
  {
    return new FHttpSessionIterator;
  }

  public function getCount()
  {
    return count($this->session());
  }

  public function getKeys()
  {
    return array_keys($this->session());
  }

  public function get($key, $defaultValue=null)
  {
    $session = $this->session($key);
    return isset($session) ? $session : $defaultValue;
  }

  public function itemAt($key)
  {
    return $this->get($key);
  }

  public function add($key,$value)
  {
    $this->sessionSet($key, $value);
  }

  public function remove($key)
  {
    if( $this->session($key) )
    {
      $value = $this->session($key);
      unset($_SESSION[$this->stateKeyPrefix][$key]);
      return $value;
    }
    else
      return null;
  }

  public function clear()
  {
    foreach(array_keys($this->session()) as $key)
      $this->remove($key);
  }

  public function contains($key)
  {
    $session = $this->session($key);

    return isset($session);
  }

  public function toArray()
  {
    return $this->session();
  }

  public function offsetExists($offset)
  {
    return $this->contains($offset);
  }

  public function offsetGet($offset)
  {
    return $this->session($offset);
  }

  public function offsetSet($offset, $item)
  {
    $this->sessionSet($offset, $item);
  }

  public function offsetUnset($offset)
  {
    $this->remove($offset);
  }

  public function getStateKeyPrefix()
  {
    if($this->_keyPrefix!==null)
      return $this->_keyPrefix;
    else
      return $this->_keyPrefix=md5('Yii.'.get_class($this).'.'.Yii::app()->getId());
  }

  public function setStateKeyPrefix($value)
  {
    $this->_keyPrefix=$value;
  }

  protected function session($key = null)
  {
    $session = Arr::get($_SESSION, $this->stateKeyPrefix, array());

    if( $key === null )
      return $session;

    return Arr::get($session, $key);
  }

  protected function sessionSet($key, $value)
  {
    $_SESSION[$this->stateKeyPrefix][$key] = $value;
  }
}