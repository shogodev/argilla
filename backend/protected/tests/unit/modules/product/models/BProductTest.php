<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BProductTest extends CDbTestCase
{
  public $fixtures = [
    'product' => 'BProduct',
    'product_assignment' => 'BProductAssignment',
  ];

  /**
   * Дата провайдер для testGetSearchCriteriaWithFilteringBySection().
   *
   * @return array
   */
  public function getSearchCriteriaWithFilteringBySectionDataProvider()
  {
    return [
      ['section_id' => 1, 'expected_count' => 4],
      ['section_id' => 2, 'expected_count' => 3],
      ['section_id' => 3, 'expected_count' => 3],
    ];
  }

  /**
   * Дата провайдер для testGetSearchCriterialWithFilteringByType().
   *
   * @return array
   */
  public function getSearchCriterialWithFilteringByTypeDataProvider()
  {
    return [
      ['type_id' => 1, 'expected_count' => 8],
      ['type_id' => 2, 'expected_count' => 2],
      ['type_id' => 3, 'expected_count' => 0],
    ];
  }

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

  // TODO: отрефакторить для DRY.

  /**
   * Проверяет критерий для поиска при фильтрации по секции.
   *
   * @property integer $section_id Идентификатор секции по которой фильтровать.
   * @property integer $expected_count Ожидаемое количество продуктов.
   *
   * @dataProvider getSearchCriteriaWithFilteringBySectionDataProvider
   */
  public function testGetSearchCriteriaWithFilteringBySection($section_id, $expected_count)
  {
    $product = new BProduct();          // Модель для фильтра.
    $product->section_id = $section_id; // Фильтруем по секции.

    // Получаем критерий для поиска всех продуктов в указанной секции.
    $criteria = $product->getSearchCriteria();

    $products = BProduct::model()->findAll($criteria);

    $this->assertContainsOnly('BProduct', $products);
    $this->assertCount($expected_count, $products);

    // Проверяем, что все уникальные.
    for ($i = 1; $i < count($products); $i++)
    {
      $this->assertNotSame($products[$i - 1], $products[$i]);
    }
  }

  /**
   * Проверяет критерий для поиска при фильтрации по типу.
   *
   * @param $type_id Идентификатор типа по которому фильтровать.
   * @param $expected_count Ожидаемое количество продуктов.
   *
   * @dataProvider getSearchCriterialWithFilteringByTypeDataProvider
   */
  public function testGetSearchCriterialWithFilteringByType($type_id, $expected_count)
  {
    // Bug: Тест не проходит после добавления новых фикстур
    $this->markTestSkipped();

    $product = new BProduct();    // Модель для фильтра.
    $product->type_id = $type_id; // Фильтруем по секции.

    // Получаем критерий для поиска всех продуктов в указанной секции.
    $criteria = $product->getSearchCriteria();

    $products = BProduct::model()->findAll($criteria);

    $this->assertContainsOnly('BProduct', $products);
    $this->assertCount($expected_count, $products);

    // Проверяем, что все уникальные.
    for ($i = 1; $i < count($products); $i++)
    {
      $this->assertNotSame($products[$i - 1], $products[$i]);
    }
  }

  // TODO: добавить тест для фильтрации по visible.
  // TODO: добавить тест для фильтрации по discount.
  // TODO: добавить тест для фильтрации по spec.
  // TODO: добавить тест для фильтрации по novelty.
  // TODO: добавить тест для фильтрации по main.
  // TODO: добавить тест для фильтрации по name.
}
