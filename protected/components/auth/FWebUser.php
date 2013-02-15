<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.auth
 */
class FWebUser extends CWebUser
{
  public $discount = 0;

  public $email = null;

  public $data = null;

  private $type = null;

  private $loginData = null;

  public function init()
  {
    parent::init();

    if( !$this->isGuest )
    {
      $this->loginData = Login::model()->findByPk($this->getId());
      $this->email     = $this->loginData->email;

      $this->type      = $this->loginData ? $this->loginData->type : 'user';

      if( $this->isUser() )
        $this->data = UserDataExtended::model()->findByPk($this->id);

      if( isset($this->loginData->discount) )
        $this->discount  = floatval($this->loginData->discount);
    }
  }

  public function isUser()
  {
    return $this->type == UserRegistration::TYPE ? true : false;
  }
}