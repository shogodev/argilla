<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */

/**
 * Class UserSocial
 *
 * @property integer $id
 * @property string $date_create
 * @property string $service_name
 * @property integer $service_id
 * @property string $email
 * @property string $name
 * @property string $user_id
 *
 * @property User $user
 */
class UserSocial extends FActiveRecord
{
  const TWITTER = 'twitter';

  const GOOGLE = 'google_oauth';

  const FACEBOOK = 'facebook';

  const VKONTAKTE = 'vkontakte';

  public function rules()
  {
    return array(
      array('service_name, service_id', 'required'),
      array('email, name', 'safe')
    );
  }

  public function relations()
  {
    return array(
      'user' => array(self::BELONGS_TO, 'User', 'user_id')
    );
  }
}