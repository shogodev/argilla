<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.productGroup
 *
 *
 */
Yii::import('backend.modules.product.ProductModule');

/**
 * Class GroupModule
 */
class GroupModule extends ProductModule
{
  public $defaultController = 'BProductGroup';
}