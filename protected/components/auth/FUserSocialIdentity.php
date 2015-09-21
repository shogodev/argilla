<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class FUserSocialIdentity extends EAuthUserIdentity
{
  public function authenticate()
  {
    if( $this->service->isAuthenticated )
    {
      $serviceManager = new SocialManager;
      $userSocial = $serviceManager->getUserSocialModel($this->service);
      $this->id = $userSocial->user_id;
      $this->errorCode = self::ERROR_NONE;
    }
    else
    {
      $this->errorCode = self::ERROR_NOT_AUTHENTICATED;
    }

    return !$this->errorCode;
  }
}