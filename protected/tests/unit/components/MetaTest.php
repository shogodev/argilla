<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.unit.components
 */
class MetaTest extends CDbTestCase
{
  protected $fixtures = array(
    'product' => 'Product',
    'product_section' => 'ProductSection',
    'product_assignment' => 'ProductAssignment',
  );

  public function setUp()
  {
    Yii::app()->setUnitEnvironment('Product', 'one', array('url' => 'new_product1'));

    parent::setUp();
  }

  public function testFindModel()
  {
    $route = 'product/one';

    $productModel = Product::model()->findByPk(1);

    $this->assertInstanceOf('Product', $productModel);

    $meta = New Meta($route);
    $meta->findModel(array('model' => $productModel));
    $meta->saveUsedModels();

    /**
     * @var MetaRoute $item
     */
    $item = MetaRoute::model()->findByAttributes(array('route' => $route));

    $this->assertNotNull($item);

    $this->assertNotEmpty($item->models, 'Models aren\'t found');

    $models = explode(',', $item->models);

    $this->assertTrue(in_array('Product', $models, 'Model "Product" isn\'t found'));
    $this->assertTrue(in_array('ProductSection', $models), 'Relation "ProductSection" isn\'t found');
    $this->assertTrue(in_array('ProductType', $models), 'Relation "ProductType" isn\'t found');
  }

  public function tearDown()
  {
    $command = Yii::app()->db->createCommand("TRUNCATE ".MetaRoute::model()->tableName());
    $command->execute();
  }
}