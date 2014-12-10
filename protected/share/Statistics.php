<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.share
 */
class Statistics
{
  protected $lengths = array();

  protected $elements;

  /**
   * @param array $elements
   */
  public function __construct(array $elements)
  {
    foreach($elements as $element)
      $this->lengths[] = count($element);

    $this->elements = $elements;
  }

  /**
   * Получаем возможные сочетания элементов массива
   *
   * @return array
   */
  public function getCombinations()
  {
    if( empty($this->elements) )
      return array();

    $combinations = array();

    foreach($this->getCounter() as $i => $combination)
    {
      foreach($combination as $j => $value)
      {
        $combinations[$i][] = $this->getElement($j, $value);
      }
    }

    return $combinations;
  }

  protected function getElement($i, $j)
  {
    return $this->elements[array_keys($this->elements)[$i]][$j];
  }

  /**
   * @return array
   */
  protected function getCounter()
  {
    $counter = array();
    $element = array_fill(0, count($this->lengths), 0);

    for($i = 0; $i < $this->getCounterLimit(); $i++)
    {
      $combination = $element;

      for($j = count($this->lengths), $number = $i; $j > 0, $number > 0; $j--, $number = $integer)
        list($integer, $combination[$j - 1]) = $this->divQr($number, $this->lengths[$j - 1]);

      $counter[] = $combination;
    }

    return $counter;
  }

  /**
   * @return integer
   */
  protected function getCounterLimit()
  {
    $limit = 1;

    foreach($this->lengths as $length)
      $limit *= $length;

    return $limit;
  }

  /**
   * Получение частного и остатка от деления
   *
   * @param integer $n
   * @param integer $d
   *
   * @return array
   */
  protected function divQr($n, $d)
  {
    return array(intval($n / $d), $n % $d);
  }
}