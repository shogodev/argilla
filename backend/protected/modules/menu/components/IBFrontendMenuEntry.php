<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu
 */
interface IBFrontendMenuEntry
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

  /**
   * @return string
   */
  public function getFrontendModelName();
}