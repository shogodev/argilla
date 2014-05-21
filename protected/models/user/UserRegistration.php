<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.user

 * @property integer $id
 * @property string $email
 * @property string $password
 */
class UserRegistration extends UserBase
{
  const TYPE = 'user';

  public $verifyCode;

  /**
   * @OVERRIDE
   *
   * @return array
   */
  public function rules()
  {
    return array(
      array('email ,password, password_confirm', 'required'),
      array('email', 'email'),
      array('email', 'unique', 'message' => 'Такой {attribute} уже используется, введите другой'),
      array('password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => 'Поля "Пароль" и "Подтверждение пароля" не совпадают'),
    );
  }

  /**
   * @OVERRIDE
   *
   * @return array
   */
  public function attributeLabels()
  {
    return CMap::mergeArray(parent::attributeLabels(), array(
      'password'   => 'Пароль',
      'address'    => 'Адрес',
      'verifyCode' => 'Код проверки',
    ));
  }

  /**
   * @OVERRIDE
   *
   * @return bool
   */
  public function beforeSave()
  {
    if( parent::beforeSave() )
    {
      $this->login = $this->email;

      $this->password = FUserIdentity::createPassword($this->login, $this->password);

      $this->type = self::TYPE;

      return true;
    }

    return false;
  }
}