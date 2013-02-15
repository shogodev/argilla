<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.soap
 */
abstract class AbstractSoapClient extends SoapClient
{
  /**
   * @param array|null $options
   */
  public function __construct($options = null)
  {
    if( $options === null )
      $options = $this->getDefaultConnectionOptions();

    parent::__construct($this->getWsdl(), $options);
  }

  /**
   * @return string
   */
  abstract public function getWsdl();

  /**
   * @return array
   */
  protected function getDefaultConnectionOptions()
  {
    return array("trace" => 1, "exception" => 0);
  }
}