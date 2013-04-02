<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BProductTest extends CDbTestCase
{
  public $fixtures = array(
    'product' => 'BProduct',
    'product_assignment' => 'BProductAssignment',
  );

  public function testMagicGet()
  {
    $product2 = BProduct::model()->findByPk(2);
    $this->assertEquals($product2->url, 'new_product2');
    $this->assertEquals($product2->section_id, '4');

    $product2->setAttributes(array(
      'section_id' => 5,
      'type_id' => '',
    ));

    $this->assertEquals($product2->section_id, '5');
    $this->assertEquals($product2->type_id, '');
  }

  public function testMagicSet()
  {
    $product2 = BProduct::model()->findByPk(2);
    $product2->setAttributes(array(
      'url' => 'new_url',
      'type_id' => '1',
    ));

    $this->assertEquals($product2->url, 'new_url');
    $this->assertEquals($product2->type_id, '1');
  }
}