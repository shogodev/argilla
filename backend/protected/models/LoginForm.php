<?php
class LoginForm extends CFormModel
{
  public $username;

  public $password;

  public $rememberMe;

  private $_identity;

  public function rules()
  {
    return array(
      array('username, password', 'required'),
      array('rememberMe', 'boolean'),
      array('password', 'authenticate'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'username' => 'Имя пользователя',
      'password' => 'Пароль',
      'rememberMe' => 'Запомнить меня',
    );
  }

  public function authenticate($attribute, $params)
  {
    if( !$this->hasErrors() )
    {
      $this->_identity = new BUserIdentity($this->username, $this->password);
      if( !$this->_identity->authenticate() )
        $this->addError('password', 'Incorrect username or password.');
    }
  }

  public function login()
  {
    if( $this->_identity === null )
    {
      $this->_identity = new BUserIdentity($this->username, $this->password);
      $this->_identity->authenticate();
    }
    if( $this->_identity->errorCode === BUserIdentity::ERROR_NONE )
    {
      $duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days
      Yii::app()->user->login($this->_identity, $duration);
      return true;
    }
    else
      return false;
  }
}