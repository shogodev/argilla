<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.auth
 * @property User $data
 * @property UserProfile $profile
 * @property string $email
 * @property string $name
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

  /**
   * @return string
   */
  public function getEmail()
  {
    if( !empty($this->getData()->email) )
      return $this->getData()->email;

    if( isset($this->getData()->socials) )
    {
      foreach($this->getData()->socials as $social)
      {
        if( !empty($social->email) )
          return $social->email;
      }
    }

    return '';
  }

  /**
   * @return string
   */
  public function getName()
  {
    if( !empty($this->getProfile()->name) )
      return $this->getProfile()->name;

    if( isset($this->getData()->socials) )
    {
      foreach($this->getData()->socials as $social)
      {
        if( !empty($social->name) )
          return $social->name;
      }
    }

    return '';
  }
}