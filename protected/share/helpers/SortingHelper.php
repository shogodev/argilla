<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class SortingHelper
{
  const ASC = 'ASC';

  const DESC = 'DESC';

  /**
   * Сортировка по позиции
   * Пример:
   * uasort($allColors, SortingHelper::sortByPosition());
   *
   * @param string $sortingType - тип сортировки (ASC/DESC - восхождени/спуск) по умолчаню ASC
   *
   * @return Closure
   */
  public static function sortByPosition($sortingType = self::ASC)
  {
    return self::sortBy(array('position'), $sortingType);
  }

  /**
   * Сортировка по позиции и имени
   * Пример:
   * uasort($allColors, SortingHelper::sortByPositionName());
   *
   * @param string $sortingType - тип сортировки (ASC/DESC - восхождени/спуск) по умолчаню ASC
   *
   * @return Closure
   */
  public static function sortByPositionName($sortingType = self::ASC)
  {
    return self::sortBy(array('position', 'name'), $sortingType);
  }

  /**
   * Сортировка по имени
   * Пример:
   * uasort($allColors, SortingHelper::sortByName());
   *
   * @param string $sortingType - тип сортировки (ASC/DESC - восхождени/спуск) по умолчаню ASC
   *
   * @return Closure
   */
  public static function sortByName($sortingType = self::ASC)
  {
    return self::sortBy(array('name'), $sortingType);
  }

  /**
   * Возвращает замыкание для сортировки
   * Пример:
   * uasort($participantsAll, SortingHelper::sortBy(array('totalScore', 'id')));
   * uasort($participantsAll, SortingHelper::sortBy(array('totalScore', 'id' => SortingHelper::ASC), SortingHelper::DESC));
   *
   * @param array $keys - масси ключий, если по первому ключу значения совпадают, то сравнение пойдет по следующему
   * @param string $defaultSortingType - ASC
   * @return Closure
   */
  public static function sortBy($keys = array(), $defaultSortingType = self::ASC)
  {
    return function($a, $b) use($keys, $defaultSortingType) {

      foreach($keys as $key => $value )
      {
        if( is_numeric($key) )
        {
          $key = $value;
          $sortingType = $defaultSortingType;
        }
        else
        {
          $sortingType = $value;
        }

        $result = self::cmp($a[$key], $b[$key], $sortingType);

        if( $result !== 0 )
          return $result;
      }

      return 0;
    };
  }

  /**
   * Сравнение позиции для сортировки
   *
   * @param $a
   * @param $b
   * @param string $sortingType - тип сортировки (ASC/DESC - восхождени/спуск) по умолчаню ASC
   * @param string $zeroPositionType - позиция нулевых и пустых элементов отоносительно направления сортировки (ASC/DESC - верх/низ) по умолчаню DESC
   *
   * @return int
   * @internal param int $zeroPosition
   */
  public static function cmp($a, $b, $sortingType = self::ASC, $zeroPositionType = self::DESC)
  {
    $multiplier = $sortingType == self::ASC ? 1 : -1;
    $zeroMultiplier = $zeroPositionType == self::ASC ? 1 : -1;

    if( empty($a) && empty($b) )
      return 0;
    else if( !empty($a) && empty($b) )
      return $zeroMultiplier;
    else if( empty($a) && !empty($b) )
      return $zeroMultiplier * -1;
    else
    {
      if( $a > $b )
        return 1 * $multiplier;
      else if( $a < $b)
        return -1 * $multiplier;
      else
        return 0;
    }
  }
}