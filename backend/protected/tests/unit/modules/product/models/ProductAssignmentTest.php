<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 19.09.12
 */
class ProductAssignmentTest extends CDbTestCase
{
  public $fixtures = array('product_assignment' => 'BProductAssignment');

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
}