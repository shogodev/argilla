<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
abstract class AbstractImportWriter
{
  /**
   * @var ConsoleFileLogger
   */
  public $logger;

  public function __construct(ConsoleFileLogger $logger)
  {
    $this->logger = $logger;
  }

  abstract public function write(array $data);
}