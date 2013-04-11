<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 *
 * @property string $label
 * @property string $name
 */
class ProductFilterElementItem extends CComponent
{
  const UNDEFINED_NAME = 'Не определено';

  public $id;

  public $selected = false;

  public $amount = 0;

  protected $label;

  /**
   * @var $parent ProductFilterElement
   */
  protected $parent;

  public function getLabel()
  {
    if( !empty($this->label) )
      return $this->label;
    else
    {
      return isset($this->parent->itemLabels[$this->id]) ? $this->parent->itemLabels[$this->id] : self::UNDEFINED_NAME;
    }
  }

  public function setLabel($label)
  {
    $this->label = $label;
  }

  public function setParent($parent)
  {
    $this->parent = $parent;
  }

  public function getParent()
  {
    return $this->parent;
  }

  public function getName()
  {
    return !empty($this->parent->name) ? $this->parent->name.'['.$this->id.']' : '';
  }

  public function isSelected()
  {
    return $this->parent->isSelectedItems($this->id);
  }

  public function isDisabled()
  {
    return isset($this->parent->disabled[$this->id]);
  }
}