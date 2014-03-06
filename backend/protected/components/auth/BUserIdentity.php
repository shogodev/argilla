<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components.auth
 */

Yii::import('backend.modules.rbac.models.BUser');

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
   * @return int
   */
  private function auth()
  {
    $id               = null;
    $username         = null;
    $password         = null;
    $requiredPassword = null;
    $visible          = null;

    if( BDevServerAuthConfig::getInstance()->isAvailable() )
    {
      $id               = 1;
      $username         = BDevServerAuthConfig::getInstance()->getUsername();
      $password         = BDevServerAuthConfig::getInstance()->getPassword();
      $visible          = 1;
      $requiredPassword = $this->password;
    }
    else
    {
      /**@var BUser $record*/
      $record = BUser::model()->findByAttributes(array('username' => $this->username));

      $id               = !empty($record) ? $record->id : null;
      $username         = !empty($record) ? $record->username : null;
      $password         = !empty($record) ? $record->password : null;
      $visible          = !empty($record) ? $record->visible : null;
      $requiredPassword = self::createPassword($this->username, $this->password);
    }

    if( $username === null )
      $this->errorCode = self::ERROR_USERNAME_INVALID;
    elseif( $password !== $requiredPassword )
      $this->errorCode = self::ERROR_PASSWORD_INVALID;
    elseif( $visible == 0 )
      $this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
    else
    {
      $this->_id = $id;
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