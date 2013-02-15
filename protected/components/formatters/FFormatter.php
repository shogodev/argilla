<?php
 /**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.formatters
 */
class FFormatter extends CFormatter
{
  /**
   * @param string $date
   *
   * @return string
   */
  public function ago($date)
  {
    $dateAgoFormatter = new DateAgoFormatter($date);
    return $dateAgoFormatter->ago();
  }
}