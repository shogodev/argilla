<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class PriceHelper
{
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
    return self::isNotEmpty($price) ? Yii::app()->format->formatNumber($price).$priceSuffix : $alternativeText;
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

    return self::isNotEmpty($economy) ? self::getPercent($economy, $oldPrice, $ceil) : 0;
  }

  /**
   * Возвращает процент числа $value от $ofValue
   *
   * @param $value
   * @param $ofValue
   * @param bool $ceil округлять до целого
   * @param int $round
   *
   * @return float
   */
  public static function getPercent($value, $ofValue, $ceil = true, $round = 1)
  {
    $percent = round(($value * 100) / $ofValue, $round);

    if( $ceil )
      $percent = ceil($percent);

    return $percent;
  }
}