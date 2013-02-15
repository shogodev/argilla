<?php
/**
 * User: tatarinov
 * Date: 17.10.12
 *
 * @property string $type
 * @property string $service
 * @property string $service_id
 */
class Login extends FActiveRecord
{
  public $login;

  public $password;

  public $rememberMe;

  private $_identity;

  public function tableName()
  {
    return '{{user}}';
  }

  public function attributeLabels()
  {
    return array(
      'login'      => 'Логин',
      'password'   => 'Пароль',
      'rememberMe' => 'Запомнить меня'
    );
  }

  public function rules()
  {
    return array(
      array('login, password', 'required'),
      array('rememberMe', 'safe')
    );
  }

  public function relations()
  {
    return array(
      'user' => array(self::HAS_ONE, 'UserDataExtended', 'user_id'),
    );
  }

  public function loginUser()
  {
    if($this->_identity === null)
    {
      $this->_identity = new FUserIdentity($this->login, $this->password);
      $this->_identity->authenticate();
    }
    if($this->_identity->errorCode === FUserIdentity::ERROR_NONE)
    {
      $duration = $this->rememberMe ? 3600*24*30 : 0; // 30 days
      Yii::app()->user->login($this->_identity, $duration);
      return true;
    }
    else
      return false;
  }

  public function getRegistrationUrl()
  {
    return Yii::app()->controller->createUrl('user/registration');
  }

  public function getRestoreUrl()
  {
    return Yii::app()->controller->createUrl('user/restore');
  }

  public function getLoginUrl()
  {
    return Yii::app()->controller->createUrl('user/login');
  }

  public function getLogoutUrl()
  {
    return Yii::app()->controller->createUrl('user/logout');
  }

  public function getButtonLogout($htmlOptions=array(), $text = '')
  {
    return CHtml::ajaxButton(
      $text,
      $this->getLogoutUrl(),
      array(
        'type'       => 'POST',
        'dataType'   => 'json',
        'beforeSend' => '$.mouseLoader(true)',
        'success'    => "function(resp){checkResponse(resp)}",
        'error'      => 'function(resp){alert("Ошибка!")}',
      ),
      $htmlOptions);
  }

  public static function getSocialAccount($id, $service)
  {
    return self::model()->findByAttributes(array(
      'service' => $service,
      'service_id' => $id,
    ));
  }

  public static function createSocialAccount($id, $service, $userData)
  {
    if( isset($id, $service) )
    {
      if( !self::getSocialAccount($id, $service) )
      {
        $model = new Login();
        $model->login      = $id."@".$service;
        $model->service    = $service;
        $model->service_id = $id;
        $model->type       = 'user';

        if( $model->save(false) )
        {
          $data = new UserDataExtended();
          $data->user_id   = $model->getPrimaryKey();
          $data->name      = Arr::get($userData, 'first_name');
          $data->last_name = Arr::get($userData, 'last_name');

          $data->save(false);
        }
      }
    }
  }
}