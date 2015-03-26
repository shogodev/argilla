<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.auth
 * @property User $data
 * @property UserProfile $profile
 */
class FWebUser extends CWebUser
{
  private $data = null;

  public function init()
  {
    parent::init();

    if( $this->isGuest )
    {
      $this->data = new User();
      $this->data->profile = new UserProfile();
    }
    else
    {
      $this->data = User::model()->findByPk($this->getId());
    }
  }

  /**
   * @return User|null
   */
  public function getData()
  {
    return $this->data;
  }

  /**
   * @return null|UserProfile
   */
  public function getProfile()
  {
    return $this->getData()->profile;
  }
}