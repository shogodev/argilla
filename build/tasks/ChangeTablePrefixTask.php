<?php
/**
 * Изменение префикса таблиц
 *
 * @author Fedor A Borshev <fedor@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package build.tasks
 *
 */
require_once 'build/tasks/DumpWorkerTask.php';

class ChangeTablePrefixTask extends DumpWorkerTask
{
  private $originalPrefix = 'argilla_';

  private $newPrefix;

  public function setNewPrefix($newPrefix)
  {
    $this->newPrefix = $newPrefix;
  }

  public function setOriginalPrefix($originalPrefix)
  {
    $this->originalPrefix = $originalPrefix;
  }

  protected function parse($fileData)
  {
    $result = array();

    foreach($fileData as $str)
    {
      $str = str_replace($this->originalPrefix, $this->newPrefix, $str);
      array_push($result, $str);
    }

    return $result;
  }
}