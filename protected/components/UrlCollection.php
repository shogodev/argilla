<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.UrlCollector
 */
class UrlCollection extends SplStack implements IApplicationComponent
{
  /**
   * @var bool
   */
  private $isInitialized = false;

  public function init()
  {
    $this->isInitialized = true;
  }

  /**
   * @return bool
   */
  public function getIsInitialized()
  {
    return $this->isInitialized;
  }
}