<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('frontend.models.product.filter.search.*');

/**
 * Class FilterTest
 */
class FilterTest extends CDbTestCase
{
  protected $fixtures = array(
    'faceted_search' => 'FacetedSearch',
  );

  /**
   * @var Filter
   */
  private $filter;

  protected function setUp()
  {
    parent::setUp();

    $this->filter = new Filter('filter', false);

    $this->filter->addElement(array(
      'id' => 'category_id',
      'itemLabels' => array(
        8 => 'Yedoo',
        9 => 'Puky'
      )
    ));

    $this->filter->addElement(array(
      'id' => '1',
      'itemLabels' => array(
        1 => 'красный',
      )
    ));

    $this->filter->addElement(array(
      'id' => 'price',
      'type' => 'range',
      'ranges' => array(
        array(0, 5000),
        array(5001, 15000),
      )
    ));

    $this->filter->addElement(array(
      'id' => 'price_old',
      'type' => 'slider',
    ));
  }

  public function testApply()
  {
    $criteria = $this->filter->apply(new CDbCriteria());
    $this->assertRegExp($this->getPatternCheckInCondition(22), $criteria->condition);
    $this->assertRegExp($this->getPatternCheckInCondition(23), $criteria->condition);
  }

  public function testEmptyStateFilter()
  {
    $criteria = new CDbCriteria();
    $this->filter->apply($criteria);

    $criteria = new CDbCriteria();
    $criteria->compare('param_id', 'category_id');
    $criteria->compare('value', 8);

    $amountByFilter = $this->filter->elements['category_id']->items[8]->amount;
    $amountInBase = FacetedSearch::model()->count($criteria);

    $this->assertEquals($amountInBase, $amountByFilter);
  }

  public function testFormatState()
  {
    $this->filter->getState()->setState(array('category_id' => array(8)));
    $criteria = $this->filter->apply(new CDbCriteria());
    $this->assertRegExp($this->getPatternCheckInCondition(22), $criteria->condition);
    $this->assertNotRegExp($this->getPatternCheckInCondition(23), $criteria->condition);

    $this->filter->getState()->setState(array('category_id' => 8));
    $criteria = $this->filter->apply(new CDbCriteria());
    $this->assertRegExp($this->getPatternCheckInCondition(22), $criteria->condition);
    $this->assertNotRegExp($this->getPatternCheckInCondition(23), $criteria->condition);
  }

  public function testApplyWithTwoSelectedValues()
  {
    $this->filter->getState()->setState(array('category_id' => array(8, 9)));
    $criteria = $this->filter->apply(new CDbCriteria());

    $this->assertRegExp($this->getPatternCheckInCondition(22), $criteria->condition);
    $this->assertRegExp($this->getPatternCheckInCondition(23), $criteria->condition);
  }

  public function testUnselectedItemsCount()
  {
    $this->filter->getState()->setState(array('category_id' => array(9)));
    $this->filter->apply(new CDbCriteria());

    $this->assertEquals(1, $this->filter->elements['category_id']->items[9]->amount);
    $this->assertArrayNotHasKey(1, $this->filter->elements);

    $this->filter->getState()->setState(array('category_id' => 8, 1 => 1));
    $this->filter->apply(new CDbCriteria());
    $this->assertEquals(0, $this->filter->elements['category_id']->items[9]->amount);
  }

  public function testSelectedItemsCount()
  {
    $this->filter->getState()->setState(array('category_id' => 8, 1 => 1));
    $this->filter->apply(new CDbCriteria());

    $this->assertEquals(1, $this->filter->elements['category_id']->items[8]->amount);

    $this->filter->getState()->setState(array('category_id' => array(9, 8)));
    $this->filter->apply(new CDbCriteria());

    $this->assertEquals(2, $this->filter->elements['category_id']->items[8]->amount);
    $this->assertEquals(1, $this->filter->elements['category_id']->items[9]->amount);
  }

  public function testRange()
  {
    $this->filter->getState()->setState(array('price' => '0-5000'));
    $criteria = $this->filter->apply(new CDbCriteria());
    $this->assertRegExp($this->getPatternCheckInCondition(25), $criteria->condition);
    $this->assertNotRegExp($this->getPatternCheckInCondition(26), $criteria->condition);
    $this->assertEquals(1, $this->filter->elements['price']->items['0-5000']->amount);
    $this->assertEquals(2, $this->filter->elements['price']->items['5001-15000']->amount);

    $this->filter->getState()->setState(array('price' => '5001-15000'));
    $criteria = $this->filter->apply(new CDbCriteria());
    $this->assertRegExp($this->getPatternCheckInCondition(26), $criteria->condition);
    $this->assertNotRegExp($this->getPatternCheckInCondition(25), $criteria->condition);
    $this->assertEquals(1, $this->filter->elements['price']->items['0-5000']->amount);
    $this->assertEquals(2, $this->filter->elements['price']->items['5001-15000']->amount);
  }

  public function testSlider()
  {
    $this->filter->getState()->setState(array('price_old' => '100-5000'));
    $criteria = $this->filter->apply(new CDbCriteria());
    $this->assertRegExp($this->getPatternCheckInCondition(25), $criteria->condition);
    $this->assertNotRegExp($this->getPatternCheckInCondition(26), $criteria->condition);
    $this->assertNotRegExp($this->getPatternCheckInCondition(27), $criteria->condition);

    $this->filter->getState()->setState(array('category_id' => array(11, 12)));
    $this->filter->apply(new CDbCriteria());
    $this->assertTrue(isset($this->filter->elements['price_old']));
  }

  /**
   * @expectedException TEndException
   */
  public function testSliderCountEmpty()
  {
    $this->filter->getState()->processState(array('price_old' => '100-200', 'category_id' => 10, 'submit' => 'amount'));

    $this->expectOutputString('{"amount":0}');
    $this->filter->apply(new CDbCriteria());
  }

  /**
   * @expectedException TEndException
   */
  public function testSliderCountNotEmpty()
  {
    $this->filter->getState()->processState(array('price_old' => '3000-5000', 'submit' => 'amount'));

    $this->expectOutputString('{"amount":1}');
    $this->filter->apply(new CDbCriteria());
  }

  /**
   * @expectedException TEndException
   */
  public function testMultiSelectWithSlider()
  {
    $this->filter->getState()->processState(array('category_id' => array(11, 12), 'price_old' => '6000-7000', 'submit' => 'amount'));

    $this->expectOutputString('{"amount":0}');
    $this->filter->apply(new CDbCriteria());
  }

  public function getPatternCheckInCondition($id)
  {
    return '/(\(|,| )('.$id.')(\)|,| )/';
  }
}