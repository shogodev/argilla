<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
interface CackleManagerInterface
{
  public function insert($item);

  public function update($item);

  public function clearAll();

  /**
   * @param $page
   * @param $limit
   * @param $modified
   *
   * @return mixed
   */
  public function getRemoteItems($page, $limit, $modified);

  public function getLastModified();

  public function getIdsForUpdate();
}