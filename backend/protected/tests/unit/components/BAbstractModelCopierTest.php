<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BAbstractModelCopierTest extends CDbTestCase
{
  protected $fixtures = array(
    'product' => 'BProduct',
    'product_assignment' => 'BProductAssignment',
  );

  protected $copier;

  public function setUp()
  {
    $this->copier = $this->getMockForAbstractClass('BAbstractModelCopier');
    parent::setUp();
  }

  public function testCopyModel()
  {
    $model = BProduct::model()->findByPk(14);

    $method = new ReflectionMethod('BAbstractModelCopier', 'copyModel');
    $method->setAccessible(true);

    $copy = $method->invoke($this->copier, $model, null, array('name' => 'copy'));
    $this->assertEquals('copy', BProduct::model()->findByPk($copy->id)->name);
  }

  public function testUnsetAttributes()
  {
    $model = BProduct::model()->findByPk(14);

    $method = new ReflectionMethod('BAbstractModelCopier', 'unsetAttributes');
    $method->setAccessible(true);

    $method->invoke($this->copier, $model, array('name'));
    $this->assertEquals(null, $model->url);
    $this->assertEquals(null, $model->name);
  }

  public function testSetAttributes()
  {
    $model = BProduct::model()->findByPk(14);

    $method = new ReflectionMethod('BAbstractModelCopier', 'setAttributes');
    $method->setAccessible(true);

    $method->invoke($this->copier, $model, array('name' => 'newName', 'someProperty' => 'value'));
    $this->assertEquals('newName', $model->name);
  }

  public function testCopyRelations()
  {
    $model = BProduct::model()->findByPk(14);

    $method = new ReflectionMethod('BAbstractModelCopier', 'copyModel');
    $method->setAccessible(true);

    $copy = $method->invoke($this->copier, $model, null, array('name' => 'copy'));

    $method = new ReflectionMethod('BAbstractModelCopier', 'copyRelations');
    $method->setAccessible(true);

    $result = $method->invoke($this->copier, $copy, $model, 'assignment');
    $this->assertTrue($result);

    $assignment = BProductAssignment::model()->findByAttributes(array('product_id' => $copy->id));
    $this->assertEquals('333', $assignment->section_id);
  }

  public function tearDown()
  {
    $this->getFixtureManager()->truncateTable('{{product}}');
    $this->getFixtureManager()->truncateTable('{{product_assignment}}');
  }
}