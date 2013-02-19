<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components.BUserIdentity
 */

Yii::import('backend.modules.rbac.models.User');

class BUserIdentity extends CUserIdentity
{
  const ALLOW_FREE_AUTH = '/usr/www/.allowFreeAuth.shogo';

  /**
   * @var string
   */
  private static $_salt;

  /**
   * @var int
   */
  private $_id;

  /**
   * Создание хэша для пароля пользователя
   *
   * @param string $username
   * @param string $password
   *
   * @return string
   */
  public static function createPassword($username, $password)
  {
    return md5($username . $password . self::getSalt());
  }

  /**
   * Аунтефикациия пользователя на сайте,
   * алгоритм которой зависит от наличии на сайте текстового файла,
   * путь которого указан self::ALLOW_FREE_AUTH
   *
   * @return int
   */
  public function authenticate()
  {
    if( file_exists(self::ALLOW_FREE_AUTH) || YII_DEBUG )
      return $this->devServerAuth() || $this->auth();
    else
      return $this->auth();
  }

  /**
   * Переопределение метода получения идентификатора пользователя с username на ID
   *
   * @return int
   */
  public function getId()
  {
    return $this->_id;
  }

  /**
   * Авторизация на dev-серверах.
   * Получение из файла self::ALLOW_FREE_AUTH значений логина и пароля
   * с присваиванием прав администратора
   *
   * @return int
   */
  private function devServerAuth()
  {
    if( !file_exists(self::ALLOW_FREE_AUTH) )
    {
      Yii::log('Файл авторизации не существует по пути '.self::ALLOW_FREE_AUTH, CLogger::LEVEL_ERROR, 'UserIdentity');
      return $this->auth();
    }

    $authFile = fopen(self::ALLOW_FREE_AUTH, 'rt');

    $username = trim(fgets($authFile));
    $password = trim(fgets($authFile));

    fclose($authFile);

    if( $username !== $this->username )
      $this->errorCode = self::ERROR_USERNAME_INVALID;
    elseif( $password !== $this->password )
      $this->errorCode = self::ERROR_PASSWORD_INVALID;
    else
    {
      $this->errorCode = self::ERROR_NONE;
      $this->_id = 1;
    }

    return !$this->errorCode;
  }

  /**
   * Обычная авторизация, проходящая по информации из базы
   *
   * @return int
   */
  private function auth()
  {
    $record = BUser::model()->findByAttributes(array('username' => $this->username));
    if( $record === null )
      $this->errorCode = self::ERROR_USERNAME_INVALID;
    elseif( $record->password !== self::createPassword($this->username, $this->password) )
      $this->errorCode = self::ERROR_PASSWORD_INVALID;
    elseif( $record->visible == 0 )
      $this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
    else
    {
      $this->_id = $record->id;
      $this->errorCode = self::ERROR_NONE;
    }

    return !$this->errorCode;
  }

  /**
   * @return string
   * @throws CException
   */
  private static function getSalt()
  {
    if ( empty(self::$_salt) )
    {
      switch( Yii::app()->params['mode'] )
      {
        case 'backend':
          self::$_salt = Yii::app()->params['salt'];
          break;

        case 'console':
          $config = require_once Yii::getPathOfAlias('backend.config') . '/backend.php';
          self::$_salt = $config['params']['salt'];
          break;

        case 'test':
          self::$_salt = 'test';
          break;

        default :
          throw new CException("Невозможно загрузить конфигурационный файл");
      }
    }

    return self::$_salt;
  }
}