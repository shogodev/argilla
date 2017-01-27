<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package ext.mainscript
 */
class CompiledScriptCreator extends ScriptAbstractCreator
{
  public function update()
  {
    if( $this->scripsExists() )
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
