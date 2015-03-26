<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.user
 *
 */
class Login extends CFormModel
{
  public $login;

  public $password;

  public $rememberMe;

  public function attributeLabels()
  {
    return array(
      'login' => 'Логин',
      'password' => 'Пароль',
      'rememberMe' => 'Запомнить меня'
    );
  }

  public function rules()
  {
    return array(
      array('login, password', 'required'),
      array('rememberMe', 'boolean'),
      array('password', 'authenticate')
    );
  }

  public function authenticate($attribute, $params)
  {
    if( !empty($this->errors) )
      return;

    $identity = new FUserIdentity($this->login, $this->password);

    if( $identity->authenticate() )
    {
      $duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days
      Yii::app()->user->login($identity, $duration);
    }
    else
    {
      $this->addError('error', 'Ошибка неверный логин/пароль!');
    }
  }
}