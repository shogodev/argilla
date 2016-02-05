<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ProductTest extends CDbTestCase
{
  protected $fixtures = array(
    'product' => 'Product',
    'product_section' => 'ProductSection',
    'product_param_name' => 'ProductParameterName',
    'product_param_variant' => 'ProductParameterVariant',
    'product_param_assignment' => 'ProductParameterAssignment',
  );

  public function testGetParameters()
  {
    $groupCriteria = new CDbCriteria();
    $groupCriteria->addInCondition('`key`', array('common', 'section'));

    /**
     * @var Product $product1
     * @var Product $product2
     */
    $product1 = Product::model()->findByPk(17);
    $product2 = Product::model()->findByPk(18);

    $parameters1 = $product1->getParameters(null, $groupCriteria);
    $parameters2 = $product2->getParameters(null, $groupCriteria);

    $this->assertCount(2, $parameters1);
    $this->assertCount(0, $parameters2);

    $this->assertEquals('value17', $parameters1[0]->value);
    $this->assertEquals('variant 14, variant 15', $parameters1[1]->value);

    $this->assertEquals('variant 14', $parameters1[1]->values[14]);
    $this->assertEquals('variant 15', $parameters1[1]->values[15]);

    $criteria = new CDbCriteria();
    $criteria->addInCondition('t.`key`', array('weight'));

    /**
     * @var Product $product1
     */
    $product1 = Product::model()->findByPk(17);
    $parameters1 = $product1->getParameters(null, null, $criteria);
    $this->assertCount(1, $parameters1);
    $this->assertEquals('value17', $parameters1[0]->value);
  }
}