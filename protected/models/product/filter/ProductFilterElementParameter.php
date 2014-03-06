<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */

class ProductFilterElementParameter extends ProductFilterElementList
{
  public $itemClass = 'ProductFilterElementVariant';

  /**
   * @var ProductParameterVariant[]
   */
  public $variants;

  public function init($parent)
  {
    $this->itemLabels = CHtml::listData($this->variants, 'id', 'name');
    parent::init($parent);
  }

  public function buildItems($items)
  {
    parent::buildItems($items);

    foreach($this->variants as $variant)
      if( isset($this->items[$variant->id]) )
        $this->items[$variant->id]->notice = $variant->notice;
  }
}