<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
class DevTool
{
  static $allocatedMemory;

  public static function allocateMemory($sizeMb)
  {
    $oneKb = str_repeat('1', 1024);
    $oneMb = str_repeat($oneKb, 1024);
    self::$allocatedMemory = str_repeat($oneMb, $sizeMb);
  }

  public static function freeMemory()
  {
    self::$allocatedMemory = null;
  }

  public static function getPid()
  {
    return getmypid();
  }

  public static function getLa()
  {
    $load = sys_getloadavg();

    return sprintf("la: %.2f,  %.2f, %.2f", $load[0], $load[1], $load[2]);
  }
}