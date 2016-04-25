<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
/**
 * Class GlobalConfig
 *
 * @property string $rootPath
 * @property string $frameworkPath
 * @property string $backendPath
 * @property string $frontendPath
 * @property string $frontendConfigPath
 * @property string $backendConfigPath
 * @property string $frameworkVersion
 */
class GlobalConfig
{
  protected $rootPath;

  protected $frameworkPath;

  protected $frontendPath;

  protected $frontendConfigPath;

  protected $backendPath;

  protected $backendConfigPath;

  /**
   * @var GlobalConfig $instance
   */
  protected static $instance;

  /**
   * @var string $frameworkVersion требуемая версия фреймворка
   */
  protected $frameworkVersion;

  public function __construct()
  {
    $this->rootPath = realpath(__DIR__.'/../../');
    $this->frontendPath = $this->rootPath.'/protected';
    $this->frontendConfigPath = $this->rootPath.'/protected/config';
    $frameworkConfig = require_once $this->frontendConfigPath.'/framework.php';

    if( !($this->frameworkPath = realpath($this->rootPath.$frameworkConfig['frameworkPath'])) )
    {
      throw new Exception('Framework path "'.$frameworkConfig['frameworkPath'].'" wasn\'t found');
    }

    $this->frameworkVersion = $frameworkConfig['version'];
    $this->backendPath = $this->rootPath.'/backend/protected';
    $this->backendConfigPath = $this->rootPath.'/backend/protected/config';
  }

  public function __get($name)
  {
    $getter = 'get'.$name;

    if( method_exists($this, $getter) )
    {
      return $this->$getter();
    }
    else if( property_exists($this, $name) )
    {
      return $this->$name;
    }

    throw new Exception('Property "'.get_class($this).'.'.$name.'" is not defined.');
  }

  /**
   * @return GlobalConfig
   */
  public static function instance()
  {
    if( isset(self::$instance) )
      return self::$instance;

    return self::$instance = new self;
  }
}

$globalConfig = GlobalConfig::instance();