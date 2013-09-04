<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ProductListTest extends CDbTestCase
{
  protected $fixtures = array(
    'product' => 'Product',
    'product_section' => 'ProductSection',
    'product_param_name' => 'ProductParameterName',
    'product_param_variant' => 'ProductParameterVariant',
    'product_param_assignment' => 'ProductParameterAssignment',
  );

  public function testSetParameters()
  {
    $criteria = new CDbCriteria();
    $criteria->compare('a.section_id', 7);

    $productList = new ProductList($criteria);
    /**
     * @var Product[] $products
     */
    $products = $productList->getProducts()->getData();

    $this->assertCount(2, $products);
    $parameters0 = $products[0]->getParameters('page');

    $this->assertEquals('variant 16', Arr::reset($parameters0)->values[16]->name);
    $this->assertEquals('variant 17', Arr::reset($parameters0)->values[17]->name);

    $parameters1 = $products[1]->getParameters('page');
    $this->assertEquals('variant 18', Arr::reset($parameters1)->value);
  }
}