<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
class LogViewerModule extends BModule
{
  public $defaultController = 'BFrontendApplicationLog';

  public $name = 'Просмотр логов';

  public $group = 'settings';
}