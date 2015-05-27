<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.user
 */
class UserModule extends BModule
{
  public $defaultController = 'BFrontendUser';
  public $name = 'Пользователи';

  protected function getExtraDirectoriesToImport()
  {
    return array('backend.modules.order.models.BOrder');
  }
}
