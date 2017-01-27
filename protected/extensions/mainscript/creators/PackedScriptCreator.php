<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package ext.mainscript
 */
class PackedScriptCreator extends ScriptAbstractCreator
{
  /**
   * Создание запакованного скрипта, собираемого из файлов ScriptsFileFinder::$files
   */
  public function create()
  {
    foreach($this->getScriptList() as $script)
    {
      $packed = fopen($script, 'a');

      foreach( ScriptsFileFinder::getInstance()->getFiles() as $file )
      {
        fwrite($packed, file_get_contents($file));
        fwrite($packed, "\n");
      }

      fclose($packed);

      chmod($script, 0664);
    }
  }

  /**
   * Удаление файла скрипта
   */
  public function delete()
  {
    foreach($this->getScriptList() as $packedFile)
    {
      if( file_exists($packedFile) )
        unlink($packedFile);
    }
  }
}
