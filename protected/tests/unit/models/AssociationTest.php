<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class AssociationTest extends CDbTestCase
{
  public $fixtures = array(
    'association' => 'Association'
  );

  /**
   * @var Association
   */
  private $association;

  public function setUp()
  {
    parent::setUp();

    $this->association = Association::model();
    $this->association->setSource(Product::model()->findByPk(1), 'Info');
  }

  public function testGetKeys()
  {
    $this->assertEquals(array(2 => 2, 3 => 3), $this->association->getKeys());
  }

  public function testGetModels()
  {
    $models = $this->association->getModels();
    $this->assertTrue($models[0] instanceof Info);
    $this->assertEquals($models[0]->id, 2);

    $this->assertTrue($models[1] instanceof Info);
    $this->assertEquals($models[1]->id, 3);
  }

  public function testGetList()
  {
    $this->association->setSource(Product::model()->findByPk(1), 'Product');

    $productList = $this->association->getList();
    $data = $productList->getDataProvider()->getData();

    $this->assertTrue($data[0] instanceof Product);
    $this->assertEquals($data[0]->id, 2);

    $this->assertTrue($data[1] instanceof Product);
    $this->assertEquals($data[1]->id, 3);
  }
} 