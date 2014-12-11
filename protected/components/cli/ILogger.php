<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */

/**
 * Interface ILogger
 */
interface ILogger
{
  /**
   * @param string $message
   */
  public function log($message);

  /**
   * @param string $message
   */
  public function error($message);

  /**
   * @param array $parameters
   */
  public function updateStatus(array $parameters);
}