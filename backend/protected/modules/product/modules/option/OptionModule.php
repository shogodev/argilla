<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('backend.modules.product.ProductModule');

class OptionModule extends ProductModule
{
  public $enabled = false;

  public $defaultController = 'BOption';
}