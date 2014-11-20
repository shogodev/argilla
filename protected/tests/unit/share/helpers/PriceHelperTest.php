<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class PriceHelperTest extends CTestCase
{
  public function testIsEmpty()
  {
    $this->assertTrue(PriceHelper::isEmpty(0));

    $this->assertFalse(PriceHelper::isEmpty(1));

    $this->assertFalse(PriceHelper::isEmpty('0.06'));

    $this->assertTrue(PriceHelper::isEmpty('0.000'));

    $this->assertFalse(PriceHelper::isEmpty('0.010'));
  }

  public function testIsNotEmpty()
  {
    $this->assertFalse(PriceHelper::isNotEmpty(0));

    $this->assertTrue(PriceHelper::isNotEmpty('0.010'));
  }

  public function testEconomy()
  {
    $this->assertEquals(PriceHelper::getEconomy(1000, 900), 100);

    $this->assertEquals(PriceHelper::getEconomy(1000.3, 900), 101);

    $this->assertEquals(PriceHelper::getEconomy(1000.3, 900, false), 100.3);

  }

  public function testPercent()
  {
    $this->assertEquals(PriceHelper::getPercent(100, 1000), 10);

    $this->assertEquals(PriceHelper::getPercent(100, 1500), 7);

    $this->assertEquals(PriceHelper::getPercent(100, 1500, false), 6.7);

    $this->assertEquals(PriceHelper::getPercent(100, 1500, false, 2), 6.67);
  }

  public function testPercentByPrice()
  {
    $this->assertEquals(PriceHelper::getEconomyPercent(1000, 900), 10);

    $this->assertEquals(PriceHelper::getEconomyPercent(1000, 875), 13);

    $this->assertEquals(PriceHelper::getEconomyPercent(1000, 875, false), 12.5);
  }

  public function testPrice()
  {
    $this->assertEquals(PriceHelper::price('0.00', ' руб', 'Звоните'), 'Звоните');

    $this->assertEquals(PriceHelper::price('1000', ' руб', 'Звоните'), '1 000 руб');

    $this->assertEquals(PriceHelper::price('1000000'), '1 000 000');
  }
}