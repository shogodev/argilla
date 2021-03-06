<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('backend.modules.product.ProductModule');

class AssignmentContentModule extends ProductModule
{
  public $enabled = false;

  public $defaultController = 'BAssignmentContent';

  public static $locations = array(
    'product' => 'В продукте',
  );

}