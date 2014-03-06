<?php
/**
 * Подготовка схемы с сохранению.
 * Заменяются дефайнеры у триггеров и вьюх на CURRENT_USER, удаляется значение AUTO_INCREMENT, удаляются лишние комментарии
 *
 * @author Fedor A Borshev <fedor@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package build.tasks
 *
 */
require_once 'build/tasks/DumpWorkerTask.php';

class FixSchemaTask extends DumpWorkerTask
{
  protected function parse($fileData)
  {
    $result = array();

    foreach($fileData as $str)
    {
      if( $this->isTriggerOrRoutine($str) || $this->isView($str) )
      {
        $str = $this->replaceUser($str);
      }

      $str = $this->removeAutoIncrement($str);
      $str = $this->removeComments($str);
      array_push($result, $str);
    }

    return $result;
  }
}