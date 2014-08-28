<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
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
 * @property string $passwordHash
 * @property string $service
 * @property string $service_id
 * @property string $restore_code
 * @property string $type
 * @property integer $visible
 *
 * @property UserProfile $profile
 */
class User extends FActiveRecord
{
  const SCENARIO_REGISTRATION = 'insert';

  const SCENARIO_CHANGE_PASSWORD = 'update';

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
      array('login, email, password, confirmPassword', 'required', 'on' => self::SCENARIO_REGISTRATION),
      array('email', 'unique', 'on' => self::SCENARIO_REGISTRATION),
      array('email', 'email', 'on' => self::SCENARIO_REGISTRATION),

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
    $this->passwordHash = FUserIdentity::createPassword($this->login, $this->password);
  }

  public function checkOldPassword($attribute, $params)
  {
    if( FUserIdentity::createPassword($this->login, $this->oldPassword) != $this->passwordHash )
      $this->addError($attribute, "Не правильно введен ".$this->getAttributeLabel($attribute));
  }
}