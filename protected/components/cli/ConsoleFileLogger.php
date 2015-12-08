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
    $this->logger->attachEventHandler('onFlush', array($this, 'onFlush'));

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
    $this->logger->flush();

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
}