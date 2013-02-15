<?php
/**
 * ChangeTablePrefixTask, Изменение префикса таблиц.
 *
 * @author Fedor A Borshev <fedor@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package build.tasks.ChangeTablePrefix
 *
 */

require_once 'build/tasks/DumpWorkerTask.php';

class ChangeTablePrefixTask extends DumpWorkerTask
{
  private $newPrefix;

  public function setNewPrefix($newPrefix)
  {
    $this->newPrefix = $newPrefix;
  }

  protected function parse($fileData)
  {
    $originalPrefix = $this->getOriginalPrefix($fileData);

    if(empty($originalPrefix))
      throw new BuildException("Cannot find original prefix in file " . $this->file);

    $result = array();
    
    foreach ($fileData as $str)
    {
      $str = str_replace($originalPrefix, $this->newPrefix, $str);

      array_push($result, $str);
    }
    return $result;
  }

  private function getOriginalPrefix ($fileData)
  {
    foreach($fileData as $str)
    {
      if(preg_match('/^-- Original prefix: /', $str))
      {
        preg_match('/^-- Original prefix: (.*_)/', $str, $q);
        return $q[1];
      }
    }
    return null;
  }
}
