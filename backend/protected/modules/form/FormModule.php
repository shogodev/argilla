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

  public function init()
  {
    $this->setImport(array(
      'form.models.*',
      'form.components.*',
      'form.controllers.*',
      'backend.modules.product.models.*',
    ));
  }
}