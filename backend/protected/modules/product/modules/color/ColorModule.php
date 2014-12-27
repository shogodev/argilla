<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 */
class ColorModule extends ProductModule
{
  public $enabled = false;

  public $defaultController = 'BColor';

  public function getThumbsSettings()
  {
    return array();
  }
}