<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class AbstractImportCommand extends CConsoleCommand
{
  public $importLogFile = 'import.log';

  /**
   * @var ConsoleFileLogger
   */
  protected $logger;

  public function init()
  {
    $this->logger = new ConsoleFileLogger($this->importLogFile);

    parent::init();
  }
}