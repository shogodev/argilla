<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class PriceHelper
{
  const LONG_DASH = '&mdash;';

  const ZERO = '0';

  const EMPTY_SPACE = '';

  /**
   * Проверяет пустое занчение
   * Поддерживает тип decimal
   * @param integer|string|decimal $value
   *
   * @return bool
   */
  public static function isEmpty($value)
  {
    $float = floatval($value);

    if( empty($float) )
      return true;

    return false;
  }

  /**
   * Проверяет не пустое занчение
   * Поддерживает тип decimal
   * @param integer|string|decimal $value
   *
   * @return bool
   */
  public static function isNotEmpty($value)
  {
    return !self::isEmpty($value);
  }

  /**
   * Форматирует цену
   * @param $price
   * @param string $priceSuffix
   * @param string $alternativeText
   *
   * @return string
   */
  public static function price($price, $priceSuffix = '', $alternativeText = '<span class="call">Звоните</span>')
  {
    return self::isNotEmpty($price) ? self::number($price).$priceSuffix : $alternativeText;
  }

  /**
   * Возвращает форматированое по правлам SFormatter число
   *
   * @param string|integer $number
   * @param string $defaultEmpty
   *
   * @return string
   */
  public static function number($number, $defaultEmpty = self::ZERO)
  {
    return  self::isNotEmpty($number) ? Yii::app()->format->formatNumber($number) : $defaultEmpty;
  }

  /**
   * Возврашает разницу цен
   * @param $oldPrice
   * @param $price
   * @param bool $ceil округлять до целого
   *
   * @return float|int
   */
  public static function getEconomy($oldPrice, $price, $ceil = true)
  {
    $economy = $oldPrice - $price;

    if( $ceil )
      $economy = ceil($economy);

    return $economy > 0 ? $economy : 0;
  }

  /**
   * Возвращает процент "экономии"
   * @param $oldPrice
   * @param $price
   * @param bool $ceil округлять до целого
   *
   * @return float
   */
  public static function getEconomyPercent($oldPrice, $price, $ceil = true)
  {
    $economy = self::getEconomy($oldPrice, $price, $ceil);

    return self::isNotEmpty($economy) ? self::getCalcPercent($economy, $oldPrice, $ceil) : 0;
  }

  /**
   * Возвращает процент числа $price от числа $ofPrice
   *
   * @param $price числа
   * @param $ofPrice числа
   * @param bool $ceil округлять до целого
   * @param int $round
   *
   * @return float процент
   */
  public static function getCalcPercent($price, $ofPrice, $ceil = true, $round = 1)
  {
    $percent = round(($price * 100) / $ofPrice, $round);

    if( $ceil )
      $percent = ceil($percent);

    return $percent;
  }

  /**
   * Возвращает число процентного соотношения $percent от числа $price
   *
   * @param $price число
   * @param $percent процент
   * @param bool $ceil округлять до целого
   *
   * @return float число
   */
  public static function getPercent($price, $percent, $ceil = true)
  {
     $discount = $percent * $price / 100;

    if( $ceil )
      $discount = ceil($discount);

    return $discount;
  }

  /**
   * Преобразует массив decimal во float
   * @param $array
   *
   * @return $array
   */
  public static function decimalToFloat($array)
  {
    array_walk($array, function(&$value) {
      $value = floatval($value);
    });

    return $array;
  }

  /**
   * Удалает из цены все символы кроме 0-9 и "."
   * @param $price
   *
   * @return mixed
   */
  public static function clear($price)
  {
    return floatval(str_replace(',', '.', preg_replace('/[^0-9,.]/', '', $price)));
  }
}