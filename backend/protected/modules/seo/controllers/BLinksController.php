<?php
/**
 * @author Alexander Kolobkov <kolobkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.seo
 */
class BLinksController extends BController
{
  public $name = 'Каталог ссылок';

  public $modelClass = 'BLinks';

  public $moduleMenu = 'BLinks';

  public $position = 10;
}