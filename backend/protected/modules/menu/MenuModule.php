<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu
 */
class MenuModule extends BModule
{
  public $defaultController = 'BMenu';
  public $name = 'Меню';

  /**
   * @return array
   */
  public function getExtraDirectoriesToImport()
  {
    return array(
      'backend.modules.menu.components.grid.*'
    );
  }
}