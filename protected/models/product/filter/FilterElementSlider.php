<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 *
 * @property integer $selectedMin;
 * @property integer $selectedMax;
 * @property integer $minValue;
 * @property integer $maxValue;
 */
class FilterElementSlider extends FilterElementRange implements JsonSerializable
{
  public $itemClass = 'FilterElementItemSlider';

  public $borderRange = 0;

  protected $minValue;

  protected $maxValue;

  protected $ranges;

  public function init($parent)
  {

  }

  public function render()
  {
    $value = !empty($this->selected) ? Arr::reset($this->selected) : null;
    echo CHtml::hiddenField($this->name, $value, array('id' => CHtml::getIdByName($this->name), 'data-value' => $value));
  }

  public function prepareAvailableValue($value)
  {
    $normalized = $this->normalizeValue($value);
    $this->setMinMaxValue($normalized);

    return $this->getValueIndex($normalized);
  }

  protected function setMinMaxValue($value)
  {
    $this->minValue = min($this->minValue !== null ? $this->minValue : $value, $value);
    $this->maxValue = max($this->maxValue, $value);
  }

  public function getSelectedMin()
  {
    if( !empty($this->selected) )
      $selected = $this->normalizeValue(explode("-", Arr::reset($this->selected))[0]);

    if( !isset($selected) )
      $selected = $this->minValue;

    return $selected;
  }

  public function getSelectedMax()
  {
    if( !empty($this->selected) )
      $selected = $this->normalizeValue(explode("-", Arr::reset($this->selected))[1]);

    if( !isset($selected) )
      $selected = $this->maxValue;

    return $selected;
  }

  public function getMinValue($range = 0)
  {
    $range = $range ? $range : $this->borderRange;

    return $this->minValue - ($range ? $this->minValue%$range : 0);
  }

  public function getMaxValue($range = 0)
  {
    $range = $range ? $range : $this->borderRange;

    return $this->maxValue + ($range ? $range - $this->maxValue%$range : 0);
  }

  public function jsonSerialize()
  {
    return array(
      $this->getMinValue(),
      $this->getMaxValue(),
      $this->getSelectedMin(),
      $this->getSelectedMax(),
    );
  }

  /**
   * @return array
   */
  protected function getRanges()
  {
    if( !isset($this->ranges) )
    {
      $this->ranges = array();

      if( $this->parent->state->isSelected($this->id) )
      {
        $this->ranges = array(explode('-', Arr::reset($this->parent->state->offsetGet($this->id))));
      }
    }

    return $this->ranges;
  }
}