<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.soap
 *
 * @example
 * System config file
 * <code>
 * login = test
 * password = test
 * wsdl = test
 * </code>
 *
 * @exapmle
 * Project config file:
 * <code>
 *  return array(
 *    'login' => 'test', 'password' => 'test', 'wsdl' => 'test',
 *  );
 * </code>
 */
class SmsSoapConfig
{
  const DEFAULT_SETTINGS_PATH      = '/etc/shogo-sms-service.ini';
  const PROJECT_SETTING_LOCAL_PATH = '/protected/config/sms.php';

  /**
   * @var SmsSoapConfig
   */
  private static $_i;

  /**
   * @var string
   */
  protected $login;

  /**
   * @var string
   */
  protected $password;

  /**
   * @var string
   */
  protected $wsdl;

  /**
   * @return SmsSoapConfig
   */
  public static function getInstance()
  {
    if( empty(self::$_i) )
      self::$_i = new self();

    return self::$_i;
  }

  private function __construct()
  {
    $this->init();
  }

  /**
   * @return string
   */
  public function getLogin()
  {
    return $this->login;
  }

  /**
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }

  /**
   * @return string
   */
  public function getWsdl()
  {
    return $this->wsdl;
  }

  /**
   * @throws SmsSoapConfigException
   */
  protected function init()
  {
    if( file_exists($this->getProjectSettingsFullPath()) )
      $this->loadProjectSettings();
    elseif( file_exists(self::DEFAULT_SETTINGS_PATH))
      $this->loadDefaultSettings();
    else
      throw new SmsSoapConfigException('Не удалось загрузить файл конфигурации для '.get_class($this));
  }

  protected function loadProjectSettings()
  {
    $data = include_once $this->getProjectSettingsFullPath();

    $this->loadSettings($data);
  }

  protected function loadDefaultSettings()
  {
    $this->loadSettings(parse_ini_file(self::DEFAULT_SETTINGS_PATH));
  }

  /**
   * @param array $data
   *
   * @throws SmsSoapConfigException
   */
  protected function loadSettings(array $data)
  {
    if( empty($data['login']) && empty($data['password']) || empty($data['wsdl']) )
      throw new SmsSoapConfigException('Неверный файл конфигурации');

    $this->login    = $data['login'];
    $this->password = $data['password'];
    $this->wsdl     = $data['wsdl'];
  }

  /**
   * @return string
   */
  protected function getProjectSettingsFullPath()
  {
    return Yii::getPathOfAlias('webroot').self::PROJECT_SETTING_LOCAL_PATH;
  }

  private function __clone(){}
}