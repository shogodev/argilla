<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components
 */
class UrlCollection extends SplStack implements IApplicationComponent
{
  public $collectUrls = false;

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