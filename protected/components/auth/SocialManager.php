<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class SocialManager extends CComponent
{
  /**
   * @param EAuthServiceBase $service
   *
   * @return UserSocial|static
   * @throws CHttpException
   */
  public function getUserSocialModel(EAuthServiceBase $service)
  {
    if( $userSocial = $this->findUserSocialModel($service) )
      return $userSocial;

    $user = $this->createUser();
    $userSocial = $this->createUserSocial($service, $user);
    $this->createUserProfile($service, $user);
    $userSocial->refresh();

    return $userSocial;
  }

  /**
   * @param EAuthServiceBase $service
   *
   * @return bool
   */
  public function isBinded(EAuthServiceBase $service)
  {
    if( $userSocial = $this->findUserSocialModel($service) )
    {
      if( $userSocial->user_id == Yii::app()->user->id )
        return true;
    }

    return false;
  }

  /**
   * @param EAuthServiceBase $service
   *
   * @throws CHttpException
   */
  public function bingSocial(EAuthServiceBase $service)
  {
    if( $userSocial = $this->findUserSocialModel($service) )
    {
      if( $userSocial->user_id != Yii::app()->user->id )
      {
        $userSocial->user->visible = false;
        $userSocial->user->save(false);

        $userSocial->user_id = Yii::app()->user->id;
        $userSocial->save(false);
      }
    }
    else
      $this->createUserSocial($service, Yii::app()->user->data);
  }

  /**
   * @param EAuthServiceBase $service
   *
   * @throws CDbException
   */
  public function unbindSocial(EAuthServiceBase $service)
  {
    if( $userSocial = $this->findUserSocialModel($service) )
    {
      $userSocial->delete();
    }
  }

  public function isAllowedUnbind()
  {
    return count(Yii::app()->user->data->socials) > 1 || !empty(Yii::app()->user->data->login);
  }

  public function getSocialList()
  {
    $socials = array(
      UserSocial::FACEBOOK => array(
        'cssClass' => 'fb',
        'bindUrl' => Yii::app()->createUrl('userProfile/bindSocial', array('service' => UserSocial::FACEBOOK)),
        'disabled' => false,
      ),
      UserSocial::VKONTAKTE => array(
        'cssClass' => 'vk',
        'bindUrl' => Yii::app()->createUrl('userProfile/bindSocial', array('service' => UserSocial::VKONTAKTE)),
        'disabled' => false,
      ),
      UserSocial::TWITTER => array(
        'cssClass' => 'twit',
        'bindUrl' => Yii::app()->createUrl('userProfile/bindSocial', array('service' => UserSocial::TWITTER)),
        'disabled' => false,
      ),
      UserSocial::GOOGLE => array(
        'cssClass' => 'gplus',
        'bindUrl' => Yii::app()->createUrl('userProfile/bindSocial', array('service' => UserSocial::GOOGLE)),
        'disabled' => false,
      )
    );

    /**
     * @var UserSocial[] $relatedSocials
     */
    $relatedSocials = Yii::app()->user->data->socials;

    foreach($relatedSocials as $social)
    {
      if( isset($socials[$social->service_name]) )
      {
        $socials[$social->service_name]['related'] = true;
        $socials[$social->service_name]['name'] = $social->name;
        $socials[$social->service_name]['disabled'] = !$this->isAllowedUnbind();
      }
    }

    return $socials;
  }

  /**
   * @param EAuthServiceBase $service
   *
   * @return UserSocial|null
   */
  private function findUserSocialModel(EAuthServiceBase $service)
  {
    if( $userSocial = UserSocial::model()->findByAttributes(array('service_name' => $service->serviceName, 'service_id' => $service->id)) )
      return $userSocial;

    return null;
  }

  /**
   * @return User
   */
  private function createUser()
  {
    $user = new User();
    $user->login = null;
    $user->type = User::TYPE_USER;
    $user->visible = 1;
    $user->save(false);

    return $user;
  }

  /**
   * @param EAuthServiceBase $service
   * @param User $user
   *
   * @return UserSocial
   * @throws CHttpException
   */
  private function createUserSocial(EAuthServiceBase $service, User $user)
  {
    $userSocial = new UserSocial();
    $userSocial->service_id = $service->id;
    $userSocial->service_name = $service->serviceName;
    $userSocial->user_id = $user->id;
    $userSocial->email = $service->getAttribute('email');
    $userSocial->name = $service->getAttribute('name');

    if( !$userSocial->save() )
      throw new CHttpException(500, 'Не удалось записать данные модели UserSocial');

    return $userSocial;
  }

  /**
   * @param $service
   * @param $user
   *
   * @return UserProfile
   */
  private function createUserProfile(EAuthServiceBase $service, User $user)
  {
    $userProfile = new UserProfile();
    $userProfile->user_id = $user->id;
    $userProfile->name = $service->getAttribute('name');
    $userProfile->birthday = $service->getAttribute('birthday');
    $userProfile->save(false);

    return $userProfile;
  }
}