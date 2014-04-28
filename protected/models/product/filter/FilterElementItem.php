<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>, Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 *
 * @property string $amount
 * @property string $label
 * @property string $name
 * @property string $cssId
 * @property string $image
 * @property Filter $filter
 */
class FilterElementItem extends CComponent
{
  const UNDEFINED_NAME = 'Не определено';

  public $id;

  protected $amount = 0;

  protected $label;

  /**
   * @var $parent FilterElement
   */
  protected $parent;

  public function getFilter()
  {
    return $this->parent->parent;
  }

  public function getLabel()
  {
    if( !isset($this->label) )
    {
      $this->setLabel(Arr::get($this->parent->itemLabels, $this->id, self::UNDEFINED_NAME));
    }

    return $this->label;
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

  public function getAmount()
  {
    return $this->amount;
  }

  public function setAmount($amount)
  {
    $this->amount = $amount;

    if( $this->amount == 0 )
    {
      $this->parent->disabled[$this->id] = $this->id;
    }
    else
    {
      unset($this->parent->disabled[$this->id]);
    }
  }

  public function getName()
  {
    if( !$this->parent->isMultiple() )
      return $this->parent->name;

    return !empty($this->parent->name) ? $this->parent->name.'['.$this->id.']' : '';
  }

  public function getCssId()
  {
    $cssId = !empty($this->parent->name) ? $this->parent->name.'['.$this->id.']' : '';

    return CHtml::getIdByName(str_replace('.', '_', $cssId));
  }

  public function isSelected()
  {
    if( $this->parent instanceof FilterElement )
    {
      return $this->parent->isItemSelected($this->id);
    }

    return false;
  }

  public function isDisabled()
  {
    return isset($this->parent->disabled[$this->id]);
  }

  public function getImage()
  {
    return new FSingleImage($this->parent->id.'_'.$this->id.'.png', 'upload/images/color');
  }

  public function render()
  {
    echo CHtml::checkBox($this->name, $this->isSelected(), array('id' => $this->cssId, 'value' => $this->id));
    echo '&nbsp;';
    echo CHtml::label($this->getLabel(), $this->cssId);
  }

  public function renderRemoveButton($content, $htmlOptions = array())
  {
    $htmlOptions['id'] = 'remove_'.$this->cssId;
    $htmlOptions['class'] = isset($htmlOptions['class']) ? $htmlOptions['class'].' remove-btn' : 'remove-btn';
    $htmlOptions['data-remove'] = $this->cssId;

    echo CHtml::link($content, '#', $htmlOptions);
  }
}