<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
class AbstractConsoleCommand extends CConsoleCommand
{
  public $basePath;

  public function init()
  {
    parent::init();

    $this->basePath = GlobalConfig::instance()->rootPath;
  }
}