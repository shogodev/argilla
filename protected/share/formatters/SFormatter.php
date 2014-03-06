<?php
 /**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.share.formatters
 */
class SFormatter extends CFormatter
{
  /**
   * @param string $date in strtotime function format
   *
   * @return string
   */
  public function ago($date)
  {
    $dateAgoFormatter = new DateAgoFormatter($date);
    return $dateAgoFormatter->ago();
  }

  /**
   * @param string $value
   *
   * @return string
   */
  public function trim($value)
  {
    return trim($value);
  }

  /**
   * @param string $value
   *
   * @return string
   */
  public function toLower($value)
  {
    return mb_strtolower($value);
  }
}