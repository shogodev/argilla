<?php
Yii::import('ext.mainscript.*');
Yii::import('ext.mainscript.creators.*');

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date 26.09.12
 * @package mainscript
 */
class ScriptFactoryTest extends CTestCase
{
  public function testBackendConfig()
  {
    $factory = new ScriptsFactory();
    $factory->mode = 'backend';
    $factory->init();

    $this->assertInstanceOf('PackedScriptCreator', $factory->getModel());
  }

  public function testFrontendDebugFalse()
  {
    $factory = new ScriptsFactory();
    $factory->mode = 'frontend';
    $factory->init();

    if( file_exists($factory->getModel()->getScript(true)) )
      $this->assertInstanceOf('CompiledScriptCreator', $factory->getModel());
    else
      $this->assertInstanceOf('PackedScriptCreator', $factory->getModel());
  }

  public function testFrontendDebugTrue()
  {
    $factory = new ScriptsFactory();
    $factory->mode = 'frontend';
    $factory->debug = true;
    $factory->init();

    $this->assertInstanceOf('PackedScriptCreator', $factory->getModel());

  }

}
