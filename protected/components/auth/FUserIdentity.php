<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.auth
 */
class FUserIdentity extends CUserIdentity
{
  private $_id;

  public function __construct($username, $password)
  {
    $this->username = User::clear($username);
    $this->password = $password;
  }

  /**
   * Создание хэша для пароля пользователя
   * @static
   * @param string $username
   * @param string $password
   * @return string
   */
  public static function createPassword($username, $password)
  {
    return md5($username . $password . self::getSalt());
  }

  /**
   * @return int
   */
  public function authenticate()
  {
    /**
     * @var $record User
     */
    $record = User::model()->findByAttributes(array('login' => $this->username));

    if( $record === null )
      $this->errorCode = self::ERROR_USERNAME_INVALID;
    elseif( $record->password_hash !== self::createPassword($this->username, $this->password) )
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
   * Переопределение метода получения ID пользователя
   * @override
   * @return int
   */
  public function getId()
  {
    return $this->_id;
  }

  /**
   * Получение уникальной соли для приложения
   * @return string
   */
  private static function getSalt()
  {
    $config = require Yii::getPathOfAlias('frontend.config.frontend').'.php';
    return $config['params']['salt'];
  }
}