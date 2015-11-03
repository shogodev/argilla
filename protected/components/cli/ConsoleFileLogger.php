<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ConsoleFileLogger extends CFileLogRoute
{
  public $showLog = true;

  /**
   * @var CFileLogRoute $logger
   */
  private $logger;

  public function __construct($fileName)
  {
    $this->logger = new CLogger();
    $this->logger->autoFlush = 20;
    $this->logger->attachEventHandler('onFlush', array($this, 'saveLogs'));

    $this->setLogFile($fileName);
    $this->init();
  }

  /**
   * @param string $message
   */
  public function log($message)
  {
    $this->logger->log($message, 'info', 'console');

    if( $this->showLog )
      echo $message.PHP_EOL;
  }

  /**
   * @param string $message
   */
  public function warning($message)
  {
    $this->logger->log($message, 'warning', 'console');

    if( $this->showLog )
      echo $message.PHP_EOL;
  }

  /**
   * @param string $message
   */
  public function error($message)
  {
    $this->logger->log($message, 'error', 'console');

    if( $this->showLog )
      echo $message.PHP_EOL;
  }

  public function saveLogs()
  {
    $this->collectLogs($this->logger, true);
  }

  public function __destruct()
  {
    $this->saveLogs();
  }
}