<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */
class FilterElementRange extends FilterElement
{
  const MAX_RANGE = 9999999;

  public $round = true;

  protected $ranges = array(
    array(0, 9999),
    array(10000, 49999),
    array(50000, self::MAX_RANGE),
  );

  public function init($parent)
  {
    if( !empty($this->itemLabels) )
    {
      throw new CException('Свойство "itemLabels" недопустимо для класса '.__CLASS__.'. Используйте свойство "ranges".');
    }

    foreach($this->getRanges() as $values)
    {
      $this->itemLabels[implode('-', $values)] = $this->toString($values[0], '', '');
    }
  }

  public function prepareAvailableValue($value)
  {
    return $this->getValueIndex($this->normalizeValue($value));
  }

  /**
   * @param mixed $value
   * @param string $prefix
   * @param string $suffix
   * @param bool $roundEnd
   *
   * @return string
   */
  public function toString($value, $prefix = '', $suffix = '', $roundEnd = true)
  {
    $range = is_array($value) ? $value : $this->getRange($value, Arr::end($this->getRanges()));
    $data = array($prefix);

    if( !empty($range[0]) )
    {
      $data[] = 'от '.PriceHelper::price($range[0], ' '.$suffix, '0');
    }

    if( $range[1] < self::MAX_RANGE )
    {
      $end = $range[1];

        if( $roundEnd )
      $end += ($range[1] % 100 ? 1 : 0);

      $data[] = 'до '.PriceHelper::price($end, ' '.$suffix, '0');
    }
    else
    {
      $data[1] = 'свыше '.PriceHelper::price($range[0], ' '.$suffix);
    }

    return Utils::ucfirst(trim(preg_replace('/\s+/', ' ', implode(' ', $data))));
  }

  /**
   * @param $ranges
   */
  protected function setRanges($ranges)
  {
    $this->ranges = $ranges;
  }

  /**
   * @return array
   */
  protected function getRanges()
  {
    return $this->ranges;
  }

  protected function getValueIndex($value)
  {
    return implode('-', $this->getRange($value, array($value)));
  }

  /**
   * @param integer $value
   * @param $default
   *
   * @return mixed
   */
  protected function getRange($value, $default)
  {
    foreach($this->getRanges() as $range)
    {
      if( $value >= $range[0] && $value <= $range[1] )
      {
        return $range;
      }
    }

    return $default;
  }

  protected function normalizeValue($value)
  {
    $value = trim($value);
    return $this->round ? intval($value) : floatval(str_replace(',', '.', $value));
  }

  protected function sortItems($items)
  {
    if( empty($items) )
      return $items;

    uasort($items, function($a, $b){
      return strnatcmp($a->id, $b->id);
    });

    return $items;
  }

  /**
   * @return string
   */
  protected function getMergeType()
  {
    return self::MERGE_TYPE_MULTIPLY_OR;
  }
}