<?php
/**
 * User: tatarinov
 * Date: 02.10.12
 *
 * @property string        $header
 * @property int           $id
 * @property string        $login
 * @property string        $phone
 * @property string        $email
 * @property string        $address
 * @property string        $password
 * @property string        $type
 * @property int           $visible
 * @property string        $date_create
 */
class User extends UserBase
{
  const DEFAULT_USER = 1;

  /**
   * @var string
   */
  public $url;

  /**
   * @var string temp password container
   */
  protected $_password;

  /**
   * Получение текущего пользователя
   *
   * @static
   *
   * @return CActiveRecord|null
   */
  public static function getCurrentUser()
  {
    if( empty(Yii::app()->user->id) )
      return null;

    return self::model()->findByPk(Yii::app()->user->id);
  }

  /**
   * @OVERRIDE
   *
   * @return array
   */
  public function rules()
  {
    return array(
      array('email', 'required'),
      array('email', 'unique'),
      array('password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => 'Поля "Новый пароль" и "Подтверждение пароля" не совпадают'),
      array('password', 'safe'),
    );
  }

  /**
   * @OVERRIDE
   *
   * @return void
   */
  public function afterFind()
  {
    $this->_password = $this->password;
    $this->password  = '';

    $this->url = Yii::app()->controller->createUrl('user/profile', array('id' => $this->id));
  }

  /**
   * @OVERRIDE
   *
   * @return bool
   */
  protected function beforeSave()
  {
    if( parent::beforeSave() )
    {
      if( empty($this->password) )
        $this->password = $this->_password;
      else
      {
        Yii::app()->notification->send('UserChangePassword', array('model' => $this), $this->email);
        $this->password = FUserIdentity::createPassword($this->login, $this->password);
      }
      return true;
    }
    return false;
  }
}