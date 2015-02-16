<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
require_once "phing/Task.php";

class CheckUseGulp extends Task
{
  private $returnProperty;

  public function main()
  {
    if ($this->returnProperty !== null) {
      $this->project->setProperty($this->returnProperty, $this->check());
    }
  }

  public function setReturnProperty($propertyName)
  {
    $this->returnProperty = $propertyName;
  }

  private function check()
  {
    $webroot = realpath(__DIR__.'/../..');
    $config = require($webroot.'/protected/config/frontend.php');

    if( isset($config['components']['mainscript']['useGulp']) && $config['components']['mainscript']['useGulp'] == true )
      return true;

    return false;
  }
}