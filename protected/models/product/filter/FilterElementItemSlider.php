<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */
class FilterElementItemSlider extends FilterElementItem
{
  /**
   * @var FilterElementSlider
   */
  public $parent;

  public function getCssId()
  {
    return CHtml::getIdByName($this->parent->name);
  }

  public function getLabel()
  {
    if( !isset($this->label) )
    {
      $this->setLabel($this->parent->toString($this->id));
    }

    return $this->label;
  }

  public function setAmount($amount)
  {
  }
}