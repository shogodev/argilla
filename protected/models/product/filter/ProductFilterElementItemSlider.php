<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */
class ProductFilterElementItemSlider extends ProductFilterElementItem
{
  /**
   * @var ProductFilterElementSlider
   */
  public $parent;

  public function getCssId()
  {
    return $this->parent->hiddenInputId;
  }
}