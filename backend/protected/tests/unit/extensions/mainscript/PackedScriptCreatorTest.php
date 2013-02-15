<?php
Yii::import('ext.mainscript.creators.*');
Yii::import('ext.mainscript.helpers.*');

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date 26.09.12
 * @package mainscript
 */
class PackedScriptCreatorTest extends CTestCase
{
  public static function tearDownAfterClass()
  {
    ScriptsFileFinder::$root = dirname(__FILE__);
    $creator = new PackedScriptCreator();

    $file = $creator->getScript(true);

    if( file_exists($file) )
      unlink($file);
  }

  public function testFileName()
  {
    $creator = new PackedScriptCreator();

    $this->assertTrue(in_array($creator->script, ScriptAbstractCreator::$scripts));
  }

  public function testCreateDelete()
  {
    ScriptsFileFinder::$root = dirname(__FILE__);
    $creator = new PackedScriptCreator();

    $this->assertFalse(file_exists($creator->getScript(true)));

    $creator->create();

    $this->assertTrue(file_exists($creator->getScript(true)));

    $creator->delete();

    $this->assertfalse(file_exists($creator->getScript(true)));
  }

  public function testGetScriptName()
  {
    ScriptsFileFinder::$root = dirname(__FILE__);
    $creator = new PackedScriptCreator();

    $this->assertNotEquals($creator->getScript(), $creator->getScript(true));
  }

  public function testUpdate()
  {
    ScriptsFileFinder::$root = dirname(__FILE__);
    $creator = new PackedScriptCreator();

    $this->assertFalse(file_exists($creator->getScript(true)));

    $creator->update();
    $this->assertTrue(file_exists($creator->getScript(true)));
  }

  public function testScriptsPath()
  {
    $creator = new PackedScriptCreator();
    $paths = array();

    foreach( PackedScriptCreator::$scripts as $script )
    {
      $paths[] = ScriptsFileFinder::$root . $creator->path . $script;
    }

    $this->assertEquals($paths, $creator->scriptsPath());
  }
}
