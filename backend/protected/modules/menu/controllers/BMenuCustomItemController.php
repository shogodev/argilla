<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu
 */
class BMenuCustomItemController extends BController
{
  /**
   * @var bool
   */
  public $enabled = false;

  /**
   * @var string
   */
  public $name = 'BFrontendCustomMenuItem';

  /**
   * @var string
   */
  public $modelClass = 'BFrontendCustomMenuItem';

  protected function getModelsAllowedForSave()
  {
    return array('data' => 'BFrontendCustomMenuItemData');
  }
}