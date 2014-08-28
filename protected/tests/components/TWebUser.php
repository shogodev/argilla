<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class TWebUser extends FWebUser
{
  protected function changeIdentity($id,$name,$states)
  {
    //Yii::app()->getSession()->regenerateID(true);
    $this->setId($id);
    $this->setName($name);
    $this->loadIdentityStates($states);
  }
}