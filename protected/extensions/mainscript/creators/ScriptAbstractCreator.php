<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package ext.mainscript
 */
abstract class ScriptAbstractCreator
{
  /**
   * Имя файла скрипта
   *
   * @var string
   */
  public $script;

  /**
   * Относительный путь к директории скриптов
   *
   * @var string
   */
  public $path = '/js/';

  /**
   * Допустимые имена скриптов
   *
   * @var array of strings
   */
  public static $scripts = array('packed.js', 'compiled.js');

  /**
   * Возвращает имя скрипта
   * Если установлено $fullPath в true,
   * то возвращает полный путь к скрипту
   *
   * @param boolean $fullPath
   *
   * @throws CException
   *
   * @return string
   */
  final public function getScript($fullPath = false)
  {
    if( empty($this->script) )
      throw new CException("Имя файла скрипта не может быть пустым");

    if( !in_array($this->script, self::$scripts) )
      throw new CException("Неверное имя или путь скрипта.");

    if( $fullPath )
      return $this->getScriptPath();
    else
      return $this->script;
  }

  /**
   * Обновление файла крипта
   * Скрипт обновляется только в случае,
   * когда обновилась контрольная сумма файлов,
   * либо не существует сам файл скриптов
   */
  public function update()
  {
    $isUpdated = ScriptHashHelper::getInstance()->isUpdated || !file_exists($this->getScriptPath());

    if( $isUpdated )
    {
      $this->delete();
      $this->create();
    }
  }

  /**
   * Возвращает полный путь к файлу скриптов
   *
   * @return string
   */
  public function getScriptPath()
  {
    return ScriptsFileFinder::getInstance()->getRoot() . $this->path . $this->script;
  }

  /**
   * Возвращает допустимые полные пути к скриптам
   *
   * @return array
   */
  public function scriptsPath()
  {
    $data = array();

    foreach( self::$scripts as $script )
    {
      $data[] = ScriptsFileFinder::getInstance()->getRoot() . $this->path . $script;
    }

    return $data;
  }

  abstract public function create();
  abstract protected function delete();
}
