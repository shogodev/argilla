<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package ext.mainscript
 */
abstract class ScriptAbstractCreator
{
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
  public static $scripts = array('packed.js', 'compiled.js', 'vendor.js', 'common.js');

  /**
   * Список имя скриптов
   *
   * @var array
   */
  protected $scriptList;


  /**
   * @throws CException
   *
   * @return array
   */
  final public function getScriptList()
  {
    if( !is_array($this->scriptList) )
      throw new CException("Свойство scriptList дожно быть массивом");

    if( empty($this->scriptList) )
      throw new CException("Имя список не может быть пустым");

    foreach($this->scriptList as $scriptName)
    if( !in_array($scriptName, static::$scripts) )
      throw new CException("Неверное имя или путь скрипта ".$scriptName);

     return $this->getScriptPath();
  }

  public function addScript($script)
  {
    $this->scriptList[] = $script;
  }

  /**
   * Обновление файла крипта
   * Скрипт обновляется только в случае,
   * когда обновилась контрольная сумма файлов,
   * либо не существует сам файл скриптов
   */
  public function update()
  {
    $isUpdated = ScriptHashHelper::getInstance()->isUpdated || !$this->scripsExists();

    if( $isUpdated )
    {
      $this->delete();
      $this->create();
    }
  }

  /**
   * Возвращает полный путь к файлу скриптов
   *
   * @return array
   */
  public function getScriptPath()
  {
    return array_map(function($scriptName) {
      return ScriptsFileFinder::getInstance()->getRoot() . $this->path . $scriptName;
    }, $this->scriptList);
  }

  public function scripsExists()
  {
    return $this->scriptListExist($this->getScriptPath());
  }

  /**
   * Возвращает допустимые полные пути к скриптам
   *
   * @return array
   */
  protected function scriptsPath()
  {
    $data = array();

    foreach( self::$scripts as $script )
    {
      $data[] = ScriptsFileFinder::getInstance()->getRoot() . $this->path . $script;
    }

    return $data;
  }

  protected function scriptListExist(array $scriptList)
  {
    foreach($scriptList as $script)
    {
      if( !file_exists($script) )
      {
        return false;
      }
    }

    return true;
  }

  abstract public function create();

  abstract protected function delete();
}
