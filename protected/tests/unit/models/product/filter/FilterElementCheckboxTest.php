<?php
class FilterElementCheckboxTest extends CTestCase
{
  /**
   * @var Filter
   */
  private $filter;

  protected function setUp()
  {
    $this->filter = new Filter('filter', false);
    parent::setUp();
  }

  public function testIsSelected()
  {
    $element = new FilterElementCheckbox();
    $element->setParent($this->filter);
    $element->id = 'category_id';

    $item = new FilterElementItem();
    $item->setParent($element);
    $item->id = 2;
    $element->items[] = $item;

    $this->assertFalse($element->isSelected());

    $this->filter->state->setState(array('category_id' => 2));
    $this->assertTrue($element->isSelected());

    $item = new FilterElementItem();
    $item->setParent($element);
    $item->id = 3;
    $element->items[] = $item;

    $this->assertTrue($element->isItemSelected(2));
    $this->assertFalse($element->isItemSelected(3));
  }
}