<?php

Yii::import('backend.components.*');
Yii::import('backend.modules.rbac.models.User');

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date 18.10.12
 * @package PasswordsCommand
 */
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
   * @var User
   */
  protected $user;

  /**
   * Создание пароля для пользователя
   *
   * @example
   * <code>
   *  //console
   *  ./yiic passwords create --user=root --password=123
   * </code>
   *
   * @param string $user
   * @param string $password
   *
   * @return 0
   */
  public function actionCreate($user, $password)
  {
    $this->username = $user;
    $this->password = $password;

    echo "----------------------------------------------------" . "\n";
    try
    {
      if( User::model()->exists('username = :username', array(':username' => $this->username)) )
        $this->user = User::model()->find('username = :username', array(':username' => $this->username));
      else
      {
        $this->user           = new User();
        $this->user->username = $this->username;
      }

      $this->user->passwordNew = $this->password;
      $this->user->save();

      echo "\tSuccess\n";
      echo "----------------------------------------------------" . "\n";
      echo "\tUser: " . $this->user->username . "\n";
      echo "\tPassword: ";
      echo $this->user->password . "\n";
    }
    catch( CException $e )
    {
      echo "\tFailure\n";
      echo "----------------------------------------------------" . "\n";
      echo "\t" . $e->getMessage() . "\n";

    }
    echo "----------------------------------------------------" . "\n";

    return 0;
  }
}