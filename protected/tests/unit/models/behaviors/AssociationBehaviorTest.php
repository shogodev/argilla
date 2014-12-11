<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class AssociationBehaviorTest extends CDbTestCase
{
  public $fixtures = array(
    'association' => 'Association'
  );

  /**
   * @var AssociationBehavior
   */
  private $behavior;

  public function setUp()
  {
    parent::setUp();
    $this->behavior = new AssociationBehavior();
  }

  public function testGetAssociationForMe()
  {
    $this->behavior->attach(Product::model()->findByPk(2));
    $data = $this->behavior->getAssociationForMe('Info');
    $this->assertEquals(array(1 => 1, 2 => 2, 3 => 3), $data->getKeys());
  }

  public function testGetAssociationWithMe()
  {
    $this->behavior->attach(Info::model()->findByPk(3));
    $data = $this->behavior->getAssociationWithMe('Product');
    $this->assertEquals(array(1 => 1, 2 => 2), $data->getKeys());
  }
} 