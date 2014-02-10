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
  public function __construct($parent)
  {
    if( isset($parent->state[Product::FILTER_PRICE]) )
    {
      $this->itemLabels = array(
        $parent->state[Product::FILTER_PRICE] => $this->toString(explode('-', $parent->state[Product::FILTER_PRICE])),
      );
    }
  }

  public function render()
  {
    $value = $this->selected ? $this->selected : '';
    echo CHtml::hiddenField($this->name, $value, array('class' => 'price-input', 'data-value' => $value));
  }
}