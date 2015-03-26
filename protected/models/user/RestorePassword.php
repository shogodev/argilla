<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.user
 *
 * @property User $user
 */
class RestorePassword extends CFormModel
{
  const GENERATE_RESTORE_CODE = 'generateRestoreCode';

  const GENERATE_NEW_PASSWORD = 'generateNewPassword';

  public $email;

  public $restoreCode;

  private $user;

  public function rules()
  {
    return array(
      array('email', 'required', 'on' => self::GENERATE_RESTORE_CODE),
      array('email', 'email', 'on' => self::GENERATE_RESTORE_CODE),
      array('email', 'exist', 'className' => 'User',  'message' => '{attribute} не найден в базе', 'on' => self::GENERATE_RESTORE_CODE),
      array('email', 'generateRestoreCode', 'on' => self::GENERATE_RESTORE_CODE),

      array('restoreCode', 'required', 'on' => self::GENERATE_NEW_PASSWORD),
      array('restoreCode', 'generateNewPassword', 'on' => self::GENERATE_NEW_PASSWORD),
    );
  }

  public function getRestoreUrl()
  {
    return Yii::app()->createAbsoluteUrl('user/restoreConfirmed', array('code' => $this->restoreCode));
  }

  public function generateRestoreCode($attribute, $params)
  {
    if( !empty($this->errors) )
      return;

    $user = $this->getUser();
    $this->restoreCode = md5(mt_rand().$user->id);
    $user->setAttribute('restore_code', $this->restoreCode);

    if( !$user->update(array('restore_code')) )
    {
      $this->addError('error', 'Ошибка генерации кода восстановления');
    }
  }

  public function generateNewPassword($attribute, $params)
  {
    if( !empty($this->errors) )
      return;

    if( $user = $this->getUser() )
    {
      $user->scenario = User::SCENARIO_CHANGE_PASSWORD;
      $user->type = User::TYPE_USER;
      $password = Utils::generatePassword(8);
      $user->setAttribute('password', $password);
      $user->doHashPassword(array(), array());
      $user->save(false);
    }
    else
      $this->addError('error', 'Неудалось найти пользователя');
  }

  /**
   * @return User
   */
  public function getUser()
  {
    if( is_null($this->user) )
    {
      $this->user = User::model()->findByAttributes(!is_null($this->email) ? array('email' => $this->email) : array('restore_code' => $this->restoreCode));
    }

    return $this->user;
  }
}
?>