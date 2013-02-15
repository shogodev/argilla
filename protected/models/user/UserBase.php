<?php
/**
 * User: tatarinov
 * Date: 25.12.12
 *
 * @property UserDataExtended $user
 * @property UserDataExtended $data
 */
class UserBase extends FActiveRecord
{
  /**
   * @var string
   */
  public $password_confirm;

  /**
   * @OVERRIDE
   *
   * @return string
   */
  public function tableName()
  {
    return '{{user}}';
  }

  /**
   * @OVERRIDE
   *
   * @return array
   */
  public function attributeLabels()
  {
    return array(
      'login'            => 'Логин',
      'email'            => 'E-mail',
      'password'         => 'Новый пароль',
      'password_confirm' => 'Подтверждение пароля',
    );
  }

  /**
   * @OVERRIDE
   *
   * @return array
   */
  public function relations()
  {
    return array(
      'data' => array(self::HAS_ONE, 'UserDataExtended', 'user_id'),
    );
  }

  /**
   * @return UserDataExtended
   */
  public function getUser()
  {
    return $this->data;
  }

  /**
   * Проверка на существование аватарки и пользователя
   *
   * @return bool
   */
  public function avatarExists()
  {
    $path = Yii::getPathOfAlias('webroot').'/'.$this->getAvatar();

    return file_exists($path) && !empty($this->data->avatar);
  }

  /**
   * @return string
   */
  public function getAvatar()
  {
    if( empty($this->user->avatar) )
      $this->user->avatar = UserDataExtended::DEFAULT_AVATAR_NAME;

    return $this->getPath().$this->user->avatar;
  }

  /**
   * @return string
   */
  protected function getPath()
  {
    return 'f/user/';
  }
}