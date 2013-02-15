<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo
 */
class BMetaRouteController extends BController
{
  public $position = 10;

  public $name = 'Маршруты';

  public $modelClass = 'BMetaRoute';

  public $moduleMenu = 'BMeta';
}