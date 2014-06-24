<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.components.collection
 *
 * @mixin FCollectionElementBehavior
 */
class TestCollectionElement extends CModel
{
  public $primaryKey = 1;

  public $price;

  public function behaviors()
  {
    return  array('collectionElement' => array('class' => 'FCollectionElementBehavior'));
  }

  public function attributeNames()
  {
  }
} 