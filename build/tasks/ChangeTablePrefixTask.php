<?php
/**
 * Изменение префикса таблиц
 *
 * @author Fedor A Borshev <fedor@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package build.tasks
 *
 */
require_once 'build/tasks/DumpWorkerTask.php';

class ChangeTablePrefixTask extends DumpWorkerTask
{
  private $originalPrefix;

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
    if( is_null($this->originalPrefix) )
      $this->originalPrefix = $this->findPrefix($fileData);

    if( is_null($this->originalPrefix) || is_null($this->newPrefix) )
      return $fileData;

    if( $this->originalPrefix == $this->newPrefix )
      return $fileData;

    echo "changing dump prefix to '{$this->newPrefix}'...".PHP_EOL;

    $result = array();

    foreach($fileData as $str)
    {
      $str = str_replace($this->originalPrefix, $this->newPrefix, $str);
      array_push($result, $str);
    }

    return $result;
  }

  protected function findPrefix($fileData)
  {
    foreach($fileData as $string)
    {
      if( preg_match('/^--?\sTablePrefix:?\s(\w+)/', $string, $matches) )
        return trim($matches[1]);
    }

    return null;
  }
}