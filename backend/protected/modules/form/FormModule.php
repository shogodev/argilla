<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.form
 */
class FormModule extends BModule
{
  public $defaultController = 'BCallback';
  public $name = 'Формы';

  /**
   * @return array
   */
  protected function getExtraDirectoriesToImport()
  {
    return [
      'backend.modules.product.models.*',
    ];
  }
}