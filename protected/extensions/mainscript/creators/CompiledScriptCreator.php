<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package ext.mainscript
 */
class CompiledScriptCreator extends ScriptAbstractCreator
{
  public $script = 'compiled.js';

  public function update()
  {
    if( file_exists($this->getScript(true)) )
      return null;
    else
      Yii::log("Создание скопмилированного файла происходит только из консоли.");
  }

  public function create()
  {
    return null;
  }

  protected function delete()
  {
    return null;
  }
}
