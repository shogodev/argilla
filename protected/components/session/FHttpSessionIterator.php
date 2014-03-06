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
class FHttpSessionIterator extends CHttpSessionIterator
{
  /**
   * @var array list of keys in the map
   */
  private $_keys;
  /**
   * @var mixed current key
   */
  private $_key;

  public function __construct()
  {
    $this->_keys = array_keys($this->session());
  }

  public function current()
  {
    return $this->session($this->_key);
  }

  public function next()
  {
    do
    {
      $this->_key=next($this->_keys);
    }
    while( !$this->session($this->_key) !== null && $this->_key !== false );
  }

  protected function session($key = null)
  {
    $session = Arr::get($_SESSION, $this->stateKeyPrefix, array());

    if( $key === null )
      return $session;

    return Arr::get($session, $key);
  }

  protected function getStateKeyPrefix()
  {
    return Yii::app()->user->stateKeyPrefix;
  }
}