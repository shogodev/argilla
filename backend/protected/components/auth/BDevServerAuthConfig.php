<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components.auth
 */
class BDevServerAuthConfig
{
  /**
   * @var BDevServerAuthConfig
   */
  private static $_i;

  /**
   * @var bool
   */
  protected $available = false;

  /**
   * @var string
   */
  protected $username;

  /**
   * @var string
   */
  protected $password;

  /**
   * @return BDevServerAuthConfig
   */
  public static function getInstance()
  {
    if( empty(self::$_i) )
      self::$_i = new self();

    return self::$_i;
  }

  public function __construct()
  {
    $this->available = $this->initLoginData();
  }

  /**
   * @return bool доступность dev-авторизации
   */
  public function isAvailable()
  {
    return $this->available;
  }

  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }

  /**
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }

  /**
   * @return string путь к файлу конфигурации
   */
  public function getPathToConfigFile()
  {
    return Yii::getPathOfAlias('backend').'/config/devAuth.php';
  }

  /**
   * @return bool флаг доступности авторизации на dev-сервере
   */
  protected function initLoginData()
  {
    $pathToConfigFile = $this->getPathToConfigFile();

    $loginData = array(
      'username' => null,
      'password' => null,
    );

    if( file_exists($pathToConfigFile) )
    {
      $config = include $pathToConfigFile;

      if( is_array($config) && isset($config['username'], $config['password']) )
        $loginData = $config;
      elseif( is_string($config) && file_exists($config) && pathinfo($config, PATHINFO_EXTENSION) === 'ini' )
        $loginData = parse_ini_file($config);
    }

    return $this->setLoginData($loginData);
  }

  /**
   * @param array $data массив с данными для логина
   *
   * @return bool если удалось присвоить значения для логина, возвращает true
   */
  protected function setLoginData(array $data)
  {
    if( !empty($data['username']) && !empty($data['password']) )
    {
      $this->username = $data['username'];
      $this->password = $data['password'];

      return true;
    }

    return false;
  }
}