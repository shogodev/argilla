<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */

/**
 * Class ConsoleLogger
 */
class ConsoleLogger implements ILogger
{
  /**
   * @param string $message
   */
  public function log($message)
  {
    echo $message.PHP_EOL;
  }

  /**
   * @param string $message
   *
   * @throws CException
   */
  public function error($message)
  {
    throw new CException($message);
  }

  /**
   * @param array $parameters
   */
  public function updateStatus(array $parameters)
  {

  }
}