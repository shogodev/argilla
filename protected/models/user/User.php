<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.user
 *
 * @method static User model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $date_create
 * @property string $login
 * @property string $password_hash
 * @property string $email
 * @property string $service
 * @property string $service_id
 * @property string $restore_code
 * @property string $type
 * @property integer $visible
 *
 * @property UserProfile $profile
 * @property UserSocial[] $socials
 */
class User extends FActiveRecord
{
  const SCENARIO_REGISTRATION = 'insert';

  const SCENARIO_CHANGE_PASSWORD = 'update';

  const SCENARIO_CHANGE_EMAIL = 'change';

  const TYPE_USER = 'user';

  /**
   * @var string
   */
  public $password;

  /**
   * @var string
   */
  public $confirmPassword;

  /**
   * @var string
   */
  public $oldPassword;

  public function tableName()
  {
    return '{{user}}';
  }

  public function rules()
  {
    return array(
      array('login, email', 'filter', 'filter' => array('User', 'clear'), 'on' => self::SCENARIO_REGISTRATION),
      array('login, email, password, confirmPassword', 'required', 'on' => self::SCENARIO_REGISTRATION),
      array('login, email', 'unique', 'on' => self::SCENARIO_REGISTRATION),
      array('email', 'email', 'on' => self::SCENARIO_REGISTRATION),

      array('email', 'email', 'on' => self::SCENARIO_CHANGE_EMAIL),
      array('email', 'filter', 'filter' => array('User', 'clear'), 'on' => self::SCENARIO_CHANGE_EMAIL),
      array('email', 'unique', 'on' => self::SCENARIO_CHANGE_EMAIL),
      array('email', 'required', 'on' => self::SCENARIO_CHANGE_EMAIL),

      array('oldPassword, password, confirmPassword', 'required', 'on' => self::SCENARIO_CHANGE_PASSWORD),
      array('oldPassword', 'checkOldPassword', 'on' => self::SCENARIO_CHANGE_PASSWORD),

      array(
        'confirmPassword',
        'compare',
        'compareAttribute' => 'password',
        'message' => 'Поля "Новый пароль" и "Подтверждение пароля" не совпадают',
        'on' => array(
          self::SCENARIO_REGISTRATION,
          self::SCENARIO_CHANGE_PASSWORD
        )
      ),

      array('password', 'doHashPassword', 'on' => array(self::SCENARIO_REGISTRATION, self::SCENARIO_CHANGE_PASSWORD)),
    );
  }

  public function attributeLabels()
  {
    return array(
      'login' => 'Логин',
      'email' => 'E-mail',
      'password' => 'Пароль',
      'confirmPassword' => 'Подтверждение пароля',
      'oldPassword' => 'Старый пароль',
    );
  }

  public function relations()
  {
    return array(
      'profile' => array(self::HAS_ONE, 'UserProfile', 'user_id'),
      'socials' => array(self::HAS_MANY, 'UserSocial', 'user_id'),
    );
  }

  public function afterValidate()
  {
    parent::afterValidate();

    if( $this->isNewRecord )
      $this->type = self::TYPE_USER;
  }

  public function doHashPassword($attribute, $params)
  {
    $this->restore_code = '';
    $this->password_hash = FUserIdentity::createPassword($this->login, $this->password);
  }

  public function checkOldPassword($attribute, $params)
  {
    if( FUserIdentity::createPassword($this->login, $this->oldPassword) != $this->password_hash )
      $this->addError($attribute, "Не правильно введен ".$this->getAttributeLabel($attribute));
  }

  public static function clear($login)
  {
    return trim(mb_strtolower($login, 'utf-8'));
  }
}