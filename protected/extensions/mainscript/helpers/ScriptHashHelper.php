<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package ext.mainscript
 */
class ScriptHashHelper
{
  /**
   * Относительный путь к файлу с версией файлов
   *
   * @var string
   */
  public $versionFile = '/js/.version';

  /**
   * Текущий хэш файлов
   *
   * @var string
   */
  public $hash;

  /**
   * Версия файлов
   *
   * @var string
   */
  public $version;

  /**
   * Массив в количеством файлов и их датой изменения
   *
   * @var array
   */
  public $data = array();

  /**
   * Флаг изменения контрольной суммы
   *
   * @var boolean
   */
  public $isUpdated;

  /**
   * @var ScriptHashHelper
   */
  private static $instance = null;

  /**
   * @return ScriptHashHelper
   */
  public static function getInstance()
  {
    if( empty(self::$instance) )
      self::$instance = new ScriptHashHelper();

    return self::$instance;
  }

  private function __construct()
  {
    $this->init();
  }

  private function __clone()
  {

  }

  /**
   * Инициализация объекта ScriptHashHelper
   */
  public function init()
  {
    $this->getHash();
    $this->getVersion();
    $this->isUpdated();

    if( $this->isUpdated )
      $this->saveVersion();
  }

  /**
   * Создание контролльной суммы,
   * основываясь на сериализации $this->data
   * получение от неё контрольной суммы по md5
   *
   * @return string
   */
  protected function getHash()
  {
    $this->data[] = count(ScriptsFileFinder::getInstance()->getFiles());

    foreach( ScriptsFileFinder::getInstance()->getFiles() as $file )
    {
      $this->data[] = filemtime($file);
    }

    $serialized = serialize($this->data);
    return $this->hash = md5($serialized);
  }

  /**
   * Инициализация флага обновления контрольной суммы
   */
  protected function isUpdated()
  {
    $this->isUpdated = $this->hash !== $this->version;
  }

  /**
   * Получение версии (предыдущей контрольной суммы)
   *
   * @return string
   */
  protected function getVersion()
  {
    $path = ScriptsFileFinder::getInstance()->getRoot() . $this->versionFile;

    if( file_exists($path) )
      $this->version = file_get_contents($path);
    else
      $this->version = 'null';

    return $this->version;
  }

  /**
   * Сохранение текущей контрольной суммы
   * (обновление версии)
   *
   * @throws Exception
   */
  protected function saveVersion()
  {
    if( empty($this->hash) )
      throw new Exception('hash is empty');

    $path = ScriptsFileFinder::getInstance()->getRoot() . $this->versionFile;

    if( file_exists($path) )
      file_put_contents($path, $this->hash);
    else
    {
      $versionFile = fopen($path, 'w');
      fwrite($versionFile, $this->hash);
      fclose($versionFile);
    }
  }

}
