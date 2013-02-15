<?php
/**
 * FixDumpTask, подготовка дампа к сохранению.
 *
 * Заменяется дефайнер у вьюх и триггеров на CURRENT_USER
 * @author Fedor A Borshev <fedor@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package build.tasks.FixDumpTask
 *
 */

require_once 'build/tasks/DumpWorkerTask.php';

class FixDumpTask extends DumpWorkerTask
{

  protected function parse($fileData)
  {
    $result = array();
    
    foreach ($fileData as $str)
    {
      if($this->isTriggerOrRoutine($str) or $this->isView($str))
      {
        $str = $this->replaceUser($str);
      }

      array_push($result, $str);
    }
    return $result;
  }

}
