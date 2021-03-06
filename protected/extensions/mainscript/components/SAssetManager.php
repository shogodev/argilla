<?php

Yii::import('ext.mainscript.*');
Yii::import('ext.mainscript.creators.*');
Yii::import('ext.mainscript.helpers.*');

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package ext.mainscript
 */
class SAssetManager extends CAssetManager
{
  /**
   * @param string $path
   * @param boolean $hashByName
   * @param int $level
   * @param boolean $forceCopy
   *
   * @return string
   */
  public function publish($path, $hashByName = false, $level = -1, $forceCopy = null)
  {
    if( in_array($path, Yii::app()->mainscript->getModel()->getScriptList()) )
      Yii::app()->mainscript->getModel()->update();

    return parent::publish($path, $hashByName, $level, $forceCopy);
  }

  /**
   * @param string $path
   *
   * @return string.
   */
  protected function hash($path)
  {
    return md5(ScriptHashHelper::getInstance()->hash . parent::hash($path));
  }

}
