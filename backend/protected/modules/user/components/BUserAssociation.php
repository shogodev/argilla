<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.user.components
 */
class BUserAssociation extends BAssociation
{
  public function getChecked($parameters)
  {
    return false;
  }
} 