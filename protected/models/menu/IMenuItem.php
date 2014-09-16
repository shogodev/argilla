<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.menu
 */
interface IMenuItem
{
  /**
   * @return array
   */
  public function getMenuUrl();

  /**
   * @return array
   */
  public function getChildren();

  /**
   * @return string
   */
  public function getName();

  /**
   * @param integer $depth
   */
  public function setDepth($depth = null);
}