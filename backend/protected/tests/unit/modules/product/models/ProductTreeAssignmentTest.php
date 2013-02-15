<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 19.09.12
 */
class ProductTreeAssignmentTest extends CDbTestCase
{
  public $fixtures = array('product_type'            => 'BProductType',
                           'product_tree_assignment' => 'BProductTreeAssignment');

  public function setUp()
  {
    parent::setUp();
  }

  public function testAssignToModel()
  {
    $model           = BProductType::model()->findByPk(1);
    $assignmentModel = BProductTreeAssignment::assignToModel($model, 'section');

    $this->assertEquals('section', $assignmentModel->dst);
    $this->assertEquals('1', $assignmentModel->dst_id);

    $assignmentModel = BProductTreeAssignment::assignToModel(new BProductType, 'section');
    $this->assertTrue($assignmentModel->isNewRecord);
  }

  public function testGetValues()
  {
    $assignmentModel = BProductTreeAssignment::assignToModel(new BProductType, 'section');
    $data            = $assignmentModel->getValues();

    foreach($data as $model)
      $this->assertInstanceOf('BProductSection', $model);
  }
}