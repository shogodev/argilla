<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class FilterElementItemRadio extends FilterElementItem
{
  public function render()
  {
    echo CHtml::radioButton($this->name, $this->isSelected(), array('id' => $this->cssId, 'value' => $this->id, 'class' => 'hidden'));
    echo '&nbsp;';
    echo CHtml::label($this->getLabel(), $this->cssId);
  }
}