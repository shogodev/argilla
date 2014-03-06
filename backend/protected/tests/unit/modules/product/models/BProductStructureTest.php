<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BProductStructureTest extends CDbTestCase
{
  protected $fixtures = array(
    'product' => 'BProduct',
    'product_section' => 'BProductSection',
    'product_type' => 'BProductType',
    'assignment' => 'BProductAssignment',
  );

  public function testSetVisible()
  {
    /**
     * @var BProductSection $section
     */
    $section = BProductSection::model()->findByPk(5);
    $section->visible = 1;
    $section->save();

    /**
     * @var BProductAssignment $assignment
     */
    $assignment = BProductAssignment::model()->findByAttributes(array('product_id' => 16));
    $this->assertEquals(1, $assignment->visible);

    /**
     * @var BProductSection $section
     */
    $section = BProductSection::model()->findByPk(6);
    $section->visible = 1;
    $section->save();

    /**
     * @var BProductAssignment $assignment
     */
    $assignment = BProductAssignment::model()->findByAttributes(array('product_id' => 17));
    $this->assertEquals(0, $assignment->visible);
  }

  public function testUnsetVisible()
  {
    /**
     * @var BProductSection $section
     */
    $section = BProductSection::model()->findByPk(7);
    $section->visible = 0;
    $section->save();

    /**
     * @var BProductAssignment $assignment
     */
    $assignment = BProductAssignment::model()->findByAttributes(array('product_id' => 18));
    $this->assertEquals(0, $assignment->visible);
  }
}