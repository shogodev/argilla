<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
require_once "phing/Task.php";

class FrameworkConfigTask extends Task
{
  protected $file;

  public function setFile($file)
  {
    $this->file = $file;
  }

  public function main()
  {
    $frameworkConfig = require($this->file);

    $this->project->setProperty('framework.path', realpath(__DIR__.'/../../'.$frameworkConfig['frameworkPath']));
    $this->project->setProperty('framework.version', $frameworkConfig['version']);
  }
}