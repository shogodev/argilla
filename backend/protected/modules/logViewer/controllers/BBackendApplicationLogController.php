<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BBackendApplicationLogController extends BBaseLogViewController
{
  public $enabled = true;

  public $position = 20;

  public $logDirPath = 'backend/protected/runtime';

  public $name = 'Backend';

  public $logFileName = 'application.log';

  public $showBy = 200;
}