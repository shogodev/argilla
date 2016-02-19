<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BFrontendApplicationLogController extends BBaseLogViewController
{
  public $enabled = true;

  public $position = 10;

  public $name = 'Frontend';

  public $logFileName = 'application.log';

  public $showBy = 200;
}