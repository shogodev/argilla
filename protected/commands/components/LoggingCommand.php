<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

Yii::import('frontend.share.helpers.Utils');
Yii::import('frontend.share.*');
Yii::import('frontend.components.cli.*');

class LoggingCommand extends AbstractConsoleCommand
{
  public $logFileName;

  public $useDummyLogSystemLog = true;

  public $loggingEnvironmentsByError = false;

  /**
   * @var ConsoleFileLogger
   */
  protected $logger;

  public function init()
  {
    parent::init();

    if( empty($this->logFileName) )
      $this->logFileName = Utils::toSnakeCase(str_replace('Command', '', get_class($this))).'.log';

    if( !Yii::app()->params['mainConsoleLogger'] )
    {
      Yii::app()->params->add('mainConsoleLogger', new ConsoleFileLogger($this->logFileName));
      Yii::app()->params['mainConsoleLogger']->log("pid = ".getmypid());
      register_shutdown_function(array($this, 'shutdownHandler'));
      $this->setErrorHandler();

      if( $this->useDummyLogSystemLog )
        Yii::setLogger(new DummyLogger());
    }

    $this->logger = Yii::app()->params['mainConsoleLogger'];

    // для --enable-pcntl
    //declare(ticks = 1);
    //pcntl_signal(SIGTERM, array($this, 'signalHandler'));
    //pcntl_signal(SIGHUP,  array($this, 'signalHandler'));
  }

  public function setErrorHandler()
  {
    set_error_handler(array($this, 'errorHandler'), E_ALL);
  }

  public function shutdownHandler()
  {
    if( is_array($error = error_get_last()) )
    {
      $code = isset($error['type']) ? $error['type'] : 0;

      if( in_array($code, array(E_ERROR, E_CORE_ERROR, 	E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR)) )
      {
        $message = isset($error['message']) ? $error['message'] : '';
        $file = isset($error['file']) ? $error['file'] : '';
        $line = isset($error['line']) ? $error['line'] : '';
        $writeMemoryToLog = strpos(strtolower($message), 'memory') !== false ? false : true;

        $this->errorHandler($code, $message, $file, $line, array(), $writeMemoryToLog);
      }
      else
        $this->logger->flush();
    }
  }

  /**
   * @param int $errNo
   * @param string $errorString
   * @param string $errorFile
   * @param string $errorLine
   * @param array $errorContext
   * @param bool $writeMemoryInfo
   *
   * @return bool
   */
  public function errorHandler($errNo , $errorString , $errorFile='',  $errorLine='', array $errorContext = array(), $writeMemoryInfo = true)
  {
    $message = $errorString;

    if( !empty($errorFile) )
      $message .= PHP_EOL.'file: '.$errorFile;
    if( !empty($errorLine) )
      $message .= PHP_EOL.'line: '.$errorLine;
    if( $this->loggingEnvironmentsByError && !empty($errorContext) )
      $message .= PHP_EOL.'details: '.PHP_EOL.implode(PHP_EOL, $errorContext);

    if( $errNo == E_ERROR )
      $this->logger->error($message, true, $writeMemoryInfo);
    else
      $this->logger->warning($message, true, true);

    return false;
  }

  /*
  function signalHandler($signalId)
  {
    switch ($signalId)
    {
      case SIGTERM:
        $this->logger->error("Caught signal SIGTERM, shutdown task", false, false);
        break;
      case SIGHUP:
        $this->logger->error("Caught signal SIGHUP, restart task", false, false);
        break;
      default:
        $this->logger->error("Caught signal id = ".$signalId, false, false);
    }
  }*/
}