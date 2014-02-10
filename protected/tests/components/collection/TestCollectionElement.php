<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.components.collection
 */
class TestCollectionElement extends CModel
{
  public $primaryKey = 1;

  public $price;

  public function behaviors()
  {
    return  array('collectionElement' => array('class' => 'FCollectionElement'));
  }

  public function attributeNames()
  {
  }
} 