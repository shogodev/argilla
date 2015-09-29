<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class SortingHelper
{
  /**
   * Сортировка по позиции
   * Пример:
   * uasort($allColors, SortingHelper::sortByPosition());
   * @return Closure
   */
  public static function sortByPosition()
  {
    return self::sortBy(array('position'));
  }

  /**
   * Сортировка по позиции и имени
   * Пример:
   * uasort($allColors, SortingHelper::sortByPositionName());
   * @return Closure
   */
  public static function sortByPositionName()
  {
    return self::sortBy(array('position', 'name'));
  }

  /**
   * Сортировка по имени
   * Пример:
   * uasort($allColors, SortingHelper::sortByName());
   * @return Closure
   */
  public static function sortByName()
  {
    return self::sortBy(array('name'));
  }

  /**
   * Возвращает замыкание для сортировки
   * @param array $keys - масси ключий, если по первому ключу значения совпадают, то сравнение пойдет по следующему
   *
   * @return Closure
   */
  public static function sortBy($keys = array())
  {
    return function($a, $b) use($keys) {

      foreach($keys as $key)
      {
        $result = self::cmp($a[$key], $b[$key]);

        if( $result !== 0 )
          return $result;
      }

      return 0;
    };
  }

  /**
   * Сравнение позиции для сортировки
   * @param $a
   * @param $b
   * @param int $zeroPosition
   *
   * @return int
   */
  public static function cmp($a, $b, $zeroPosition = -1)
  {
    if( empty($a) && empty($b) )
      return 0;
    else if( !empty($a) && empty($b) )
      return $zeroPosition;
    else if( empty($a) && !empty($b) )
      return $zeroPosition * -1;
    else
    {
      if( $a > $b )
        return 1;
      else if( $a < $b)
        return -1;
      else
        return 0;
    }
  }
}