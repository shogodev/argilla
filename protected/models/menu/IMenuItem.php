<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.menu
 */
interface IMenuItem
{
  public function getMenuLink();

  public function getChildren();

  public function getName();

  public function setDepth($d);
}