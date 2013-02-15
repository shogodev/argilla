<?php
/**
 * User: tatarinov
 * Date: 17.10.12
 */
class UserRestore extends UserBase
{
  public function rules()
  {
    return array(
      array('email', 'required', 'message' => 'Не заполнено обязательное поле "{attribute}"'),
      array('email', 'email', 'message' => 'Не верно введен {attribute}'),
      array('email', 'exist', 'message' => 'Указанный {attribute} не найден в базе'),
    );
  }

  public function getRestoreUrl()
  {
    return Yii::app()->request->hostInfo.Yii::app()->controller->createUrl('user/restoreConfirmed', array('code' => $this->restore_code)) ;
  }

  public function generateRestoreCode()
  {
    $this->restore_code = md5(mt_rand().$this->id);
    if( $this->save() )
    {
      Yii::app()->notification->send('ConfirmPasswordRestore',
                                      array(
                                        'model'      => $this,
                                        'restoreUrl' => $this->getRestoreUrl()
                                        ),
                                      $this->email);
      return true;
    }
    return false;
  }

  public function generateNewPassword()
  {
    $password           = Utils::generatePassword(8);
    $this->password     = FUserIdentity::createPassword($this->login, $password);
    $this->restore_code = '';
    if( $this->save() )
    {
      Yii::app()->notification->send('UserRestorePassword',
                                     array(
                                       'model'    => $this,
                                       'password' => $password
                                       ),
                                     $this->email);
      return true;
    }
    return false;
  }
}
?>