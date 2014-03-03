<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */
class ProductFilterElementSlider extends ProductFilterElementRange
{
  public $itemClass = 'ProductFilterElementItemSlider';

  public $hiddenInputId = 'filter-price-input';

  public function __construct($parent)
  {
    if( isset($parent->state[ProductFilter::FILTER_PRICE]) )
    {
      $this->itemLabels = array(
        $parent->state[ProductFilter::FILTER_PRICE] => $this->toString(explode('-', $parent->state[ProductFilter::FILTER_PRICE])),
      );
    }
  }

  public function render()
  {
    $value = $this->selected ? $this->selected : null;
    echo CHtml::hiddenField($this->name, $value, array('id' => $this->hiddenInputId, 'data-value' => $value));
  }

  public function getRanges()
  {
    return CJavaScript::jsonEncode(array(
      $this->minValue,
      $this->maxValue,
      $this->selectedMin,
      $this->selectedMax,
    ));
  }
}