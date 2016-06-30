<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('frontend.components.cli.*');
class DummyConsoleFileLogger extends ConsoleFileLogger
{
  public $testLog = array('info' => array(), 'warning' => array(), 'error' => array());

  public function __construct($fileName)
  {
  }

  /**
   * @param string $message
   * @param bool $writeNow
   * @param bool $writeMemoryUsage
   */
  public function log($message, $writeNow = false, $writeMemoryUsage = false)
  {
    $this->testLog['info'][] = $message;
  }

  /**
   * @param string $message
   * @param bool $writeNow
   * @param bool $writeMemoryUsage
   */
  public function warning($message, $writeNow = false, $writeMemoryUsage = false)
  {
    $this->testLog['warning'][] = $message;
  }

  /**
   * @param string $message
   * @param bool $writeNow
   * @param bool $writeMemoryUsage
   */
  public function error($message, $writeNow = false, $writeMemoryUsage = true)
  {
    $this->testLog['error'][] = $message;
  }


}