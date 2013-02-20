<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.info
 */
class InfoModule extends BModule
{
  public $defaultController = 'BInfo';
  public $name = 'Информация';

  public function getThumbsSettings()
  {
    return array('info' => array('pre' => array(100, 100)));
  }
}