<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu.components
 */
interface IBFrontendMenuEntry extends IHasFrontendModel
{
  /**
   * @return int|string
   */
  public function getId();

  /**
   * @return string
   */
  public function getName();

  /**
   * @return string
   */
  public function getUrl();
}