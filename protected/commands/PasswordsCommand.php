<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */

Yii::import('backend.components.db.*');
Yii::import('backend.components.auth.*');
Yii::import('backend.modules.rbac.models.*');

class PasswordsCommand extends CConsoleCommand
{
  /**
   * @var string
   */
  protected $username;

  /**
   * @var string
   */
  protected $password;

  /**
   * @var BUser
   */
  protected $user;

  /**
   * Обновление пароля у пользователя / Добавление нового пользователя
   *
   * @param string $user
   * @param string $password
   *
   * @return int
   */
  public function actionCreate($user, $password)
  {
    $this->username = $user;
    $this->password = $password;

    try
    {
      $criteria = new CDbCriteria();
      $criteria->compare('username', $this->username);

      $this->user = BUser::model()->find($criteria);

      if( $this->user === null )
      {
        $this->user           = new BUser();
        $this->user->username = $this->username;
      }
      elseif( !$this->confirm('Пользователь уже существует, обновить его пароль?', false) )
        return 1;

      $this->user->setNewPassword($this->password);
      $this->user->save();

      echo "----------------------------------------------------".PHP_EOL;
      echo "\tSuccess".PHP_EOL;
      echo "----------------------------------------------------".PHP_EOL;
      echo "\tUser: " . $this->user->username.PHP_EOL;
      echo "\tPassword: ";
      echo $this->password.PHP_EOL;
    }
    catch( CException $e )
    {
      echo "\tFailure".PHP_EOL;
      echo "----------------------------------------------------".PHP_EOL;
      echo "\t".$e->getMessage().PHP_EOL;

    }
    echo "----------------------------------------------------".PHP_EOL;

    return 0;
  }
}