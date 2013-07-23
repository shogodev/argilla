<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
/**
 * @method BProduct product(string $alias)
 * @method BProductAssignment assignment(string $alias)
 */
class BProductTest extends CDbTestCase
{
  protected $fixtures = array(
    'product' => 'BProduct',
    'assignment' => 'BProductAssignment',
  );


  public function testMagicGet()
  {
    $product2 = BProduct::model()->findByPk(2);
    $this->assertEquals('new_product2', $product2->url);
    $this->assertEquals('4', $product2->section_id);

    $product2->setAttributes(array(
      'section_id' => 5,
      'type_id' => '',
    ));

    $this->assertEquals('5', $product2->section_id);
    $this->assertEquals('', $product2->type_id);
  }

  public function testMagicSet()
  {
    $product2 = BProduct::model()->findByPk(2);
    $product2->setAttributes(array(
      'url' => 'new_url',
      'type_id' => '1',
    ));

    $this->assertEquals('new_url', $product2->url);
    $this->assertEquals('1', $product2->type_id);
  }

  public function testSearchWithFilteringBySection()
  {
    $product = new BProduct();
    $product->section_id = 2;

    $products = $product->search()->getData();

    $this->assertNotEmpty($products);
    $this->assertContainsOnly('BProduct', $products);

    $this->assertTrue($this->product('product4')->equals($products[0]));
    $this->assertTrue($this->product('product7')->equals($products[1]));
    $this->assertTrue($this->product('product8')->equals($products[2]));

    $this->assertTrue($this->areAllActiveRecordsUnique($products));
  }

  public function testSearchWithFilteringBySectionWhenThereAreNoAssignedProducts()
  {
    $product = new BProduct();
    $product->section_id = 9001;

    $products = $product->search()->getData();

    $this->assertEmpty($products);
  }

  public function testSearchWithFilteringByType()
  {
    $product = new BProduct();
    $product->type_id = 42;

    $products = $product->search()->getData();

    $this->assertNotEmpty($products);
    $this->assertContainsOnly('BProduct', $products);

    $this->assertTrue($this->product('product1')->equals($products[0]));
    $this->assertTrue($this->product('product2')->equals($products[1]));
    $this->assertTrue($this->product('product3')->equals($products[2]));
    $this->assertTrue($this->product('product4')->equals($products[3]));
    $this->assertTrue($this->product('product5')->equals($products[4]));
    $this->assertTrue($this->product('product6')->equals($products[5]));
    $this->assertTrue($this->product('product8')->equals($products[6]));
    $this->assertTrue($this->product('product9')->equals($products[7]));

    $this->assertTrue($this->areAllActiveRecordsUnique($products));
  }

  public function testSearchWithFilteringByTypeWnenThereAreNoAssignedProducts()
  {
    $product = new BProduct();
    $product->type_id = 9001;

    $products = $product->search()->getData();

    $this->assertEmpty($products);
  }


  /**
   * @param CActiveRecord[] $activeRecords
   *
   * @return bool
   */
  private function areAllActiveRecordsUnique(array $activeRecords)
  {
    for ($i = 1; $i < count($activeRecords); $i++)
    {
      if( $activeRecords[$i - 1]->equals($activeRecords[$i]) )
      {
        return false;
      }
    }

    return true;
  }
}