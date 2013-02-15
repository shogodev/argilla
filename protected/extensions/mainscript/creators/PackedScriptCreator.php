<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package ext.mainscript
 */
class PackedScriptCreator extends ScriptAbstractCreator
{
  public $script = 'packed.js';

  /**
   * Создание запакованного скрипта, собираемого из файлов ScriptsFileFinder::$files
   */
  public function create()
  {
    $packed = fopen($this->getScript(true), 'a');

    foreach( ScriptsFileFinder::getInstance()->getFiles() as $file )
    {
      fwrite($packed, file_get_contents($file));
      fwrite($packed, "\n");
    }

    fclose($packed);
  }

  /**
   * Удаление файла скрипта
   */
  public function delete()
  {
    $packedFile  = $this->getScript(true);

    if( file_exists($packedFile) )
      unlink($packedFile);
  }
}
