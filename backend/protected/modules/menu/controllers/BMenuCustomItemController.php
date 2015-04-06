<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu.controllers
 */
class BMenuCustomItemController extends BController
{
  public $showInMenu = false;

  /**
   * @var string
   */
  public $name = 'Попап пользовательского меню';

  /**
   * @var string
   */
  public $modelClass = 'BFrontendCustomMenuItem';

  protected function getModelsAllowedForSave()
  {
    return array('data' => 'BFrontendCustomMenuItemData');
  }
}