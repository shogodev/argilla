<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('frontend.share.formatters.*');

class ConsoleFileLogger extends CFileLogRoute
{
  public $showLog = true;

  private $timers = array();

  /**
   * @var CFileLogRoute $logger
   */
  private $logger;

  public function __construct($fileName)
  {
    $this->logger = new CLogger();
    $this->logger->autoFlush = 20;
    $this->logger->attachEventHandler('onFlush', array($this, 'onFlush'));

    $this->setLogFile($fileName);
    $this->init();
  }

  /**
   * @param string $message
   * @param bool $writeNow
   * @param bool $writeMemoryUsage
   */
  public function log($message, $writeNow = false, $writeMemoryUsage = false)
  {
    if( $writeMemoryUsage )
      $this->addMemoryUsage($message);

    $this->logger->log($message, 'info', 'console');

    if( $writeNow )
      $this->flush();

    if( $this->showLog )
      echo $message.PHP_EOL;
  }

  /**
   * @param string $message
   * @param bool $writeNow
   * @param bool $writeMemoryUsage
   */
  public function warning($message, $writeNow = false, $writeMemoryUsage = false)
  {
    if( $writeMemoryUsage )
      $this->addMemoryUsage($message);

    $this->logger->log($message, 'warning', 'console');

    if( $writeNow )
      $this->flush();

    if( $this->showLog )
      echo $message.PHP_EOL;
  }

  /**
   * @param string $message
   * @param bool $writeNow
   * @param bool $writeMemoryUsage
   */
  public function error($message, $writeNow = true, $writeMemoryUsage = true)
  {
    if( $writeMemoryUsage )
      $this->addMemoryUsage($message);

    $this->logger->log($message, 'error', 'console');

    if( $writeNow )
      $this->flush();

    if( $this->showLog )
      echo $message.PHP_EOL;
  }

  /**
   * Сохраняет лог в файл с очисткой сообщений из памяти
   */
  public function flush()
  {
    $this->logger->flush();
  }

  public function __destruct()
  {
    $this->logger->flush();
  }

  /**
   * Сохраняет лог в файл, сообщения остаются в памяти при прямом вызове метода
   */
  public function onFlush()
  {
    $this->collectLogs($this->logger, true);
  }

  public function addMemoryUsage(&$message)
  {
    $message .= PHP_EOL.'Использовано памяти: '.Yii::app()->format->formatSize(memory_get_usage());
    $message .= ', пик: '.Yii::app()->format->formatSize(memory_get_peak_usage());
    $load = sys_getloadavg();
    $message .= sprintf(", la: %.2f,  %.2f, %.2f", $load[0], $load[1], $load[2]).PHP_EOL;
  }

  public function formatTime($time)
  {
    return sprintf("%d мин. %d с.", $time / 60, $time % 60);
  }

  public function startTimer($timerId)
  {
    if( isset($this->timers[$timerId]) )
      throw new Exception("Таймер в с ".$timerId." уже создан");

    $this->timers[$timerId] = microtime(true);
  }

  public function finishTimer($timerId, $message = '')
  {
    if( !isset($this->timers[$timerId]) )
      throw new Exception("Таймер в с ".$timerId."  не был создан");

    $time = microtime(true) - $this->timers[$timerId];

    $message .= $this->formatTime($time);

    return $message;
  }
}