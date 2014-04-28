<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 19.09.12
 */
class ProductAssignmentTest extends CDbTestCase
{
  protected $fixtures = array('product_assignment' => 'BProductAssignment');

  public function setUp()
  {
    parent::setUp();
  }

  public function testGetDepends()
  {
    $model = BProductAssignment::model()->findByPk(1);
    $data  = $model->getDepends('section_id', 'type_id');

    $this->assertCount(3, $data);

    foreach($data as $model)
      $this->assertInstanceOf('BProductType', $model);
  }

  public function testSaveAssignments()
  {
    $product = BProduct::model()->findByPk(19);

    BProductAssignment::model()->saveAssignments($product, array('section_id' => 1));
    $productAssignments = BProductAssignment::model()->findByAttributes(array('product_id' => 19));
    $this->assertEquals(19, $productAssignments->id);

    BProductAssignment::model()->saveAssignments($product, array('section_id' => 2));
    $this->assertEquals(2, $product->section_id);

    BProductAssignment::model()->saveAssignments($product, array('section_id' => 2, 'type_id' => array(1, 2)));
    $assignments = BProductAssignment::model()->findAllByAttributes(array('product_id' => 19));
    $this->assertCount(2, $assignments);
  }
}