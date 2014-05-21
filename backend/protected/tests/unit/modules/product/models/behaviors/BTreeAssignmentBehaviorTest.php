<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BTreeAssignmentBehaviorTest extends CDbTestCase
{
  protected $fixtures = array(
    'product_type' => 'BProductType',
    'product_section' => 'BProductSection',
    'product_tree_assignment' => 'BProductTreeAssignment'
  );

  private $model;

  public function setUp()
  {
    parent::setUp();
  }

  /**
   * @expectedException CHttpException
   */
  public function testAttach()
  {
    $model = new BProductType();
    $model->attachBehavior('wrongBehavior', array('class' => 'BTreeAssignmentBehavior'));
  }

  public function testInsertAndDelete()
  {
    $model = new BProductType();

    if( !$model->asa('tree') )
    {
      $model->attachBehavior('tree', array(
        'class' => 'BTreeAssignmentBehavior',
        'parentModel' => 'BProductSection'
      ));
    }

    $model->name = 'test BTreeAssignmentBehavior';
    $model->url = 'test_b_tree_assignment_behavior';
    $model->parent_id = 1;

    $this->assertTrue($model->save());
    $this->assertInstanceOf('BProductSection', $model->parent);
    $this->assertEquals($model->parent_id, 1);

    $model->delete();

    $treeAssignmentModel = BProductTreeAssignment::model()->findByAttributes(array(
      'src' => 'type',
      'src_id' => $model->id,
      'dst' => 'section',
      'dst_id' => 1
    ));

    $this->assertNull($treeAssignmentModel);
  }

  public function testSearch()
  {
    /**
     * @var BProductType $model
     */
    $model = BProductType::model()->findByPk(1);

    if( !$model->asa('tree') )
    {
      $model->attachBehavior('tree', array(
        'class' => 'BTreeAssignmentBehavior',
        'parentModel' => 'BProductSection'
      ));
    }

    $dataProvider = $model->search();
    $this->assertContains('parent.id', $dataProvider->getCriteria()->condition);
    $this->assertContains('parent', $dataProvider->getCriteria()->with);
  }

  public function testGetParents()
  {
    /**
     * @var BProductType $model
     */
    $model = BProductType::model()->findByPk(1);

    if( !$model->asa('tree') )
    {
      $model->attachBehavior('tree', array(
        'class' => 'BTreeAssignmentBehavior',
        'parentModel' => 'BProductSection'
      ));
    }

    $parents = $model->getParents();
    $this->assertInstanceOf('BProductSection', $parents[0]);
  }
}