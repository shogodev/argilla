<?php
class FacetedDataProcessorTest extends CTestCase
{
  /**
   * @var FilterState
   */
  private $state;

  protected function setUp()
  {
    $this->state = new FilterState('filter', false);

    parent::setUp();
  }

  public function testGetSelectedIds()
  {
    $this->state->setState(array('price' => array(), 'category' => array(1 => 1)));

    $selectedData = new FilterProcessor($this->state, array());
    $selectedData->prepare('category', 1, 1);

    $processor = new FacetedDataProcessor($selectedData);
    $ids = $processor->getFilteredIds($selectedData);
    $this->assertEmpty($ids);

    $this->state->setState(array('category' => array(1 => 1), 'price' => array()));
    $selectedData = new FilterProcessor($this->state, array());
    $selectedData->prepare('category', 1, 1);
    $ids = $processor->getFilteredIds($selectedData);
    $this->assertEmpty($ids);
  }

  public function testIsSelectedElementItem()
  {
    $this->state->setState(array('category' => array(1 => 1)));
    $selectedData = new FilterProcessor($this->state, array());
    $processor = new FacetedDataProcessor($selectedData);

    $processor->prepare('category', 1, 1);
    $processor->prepare('category', 1, 2);
    $processor->prepare('category', 1, 3);

    $processor->getFilteredIds($selectedData);
    $amountItems = $processor->getAmounts();

    $this->assertEquals(array('category' => array(1 => 3)), $amountItems);
  }
}