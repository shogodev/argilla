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
    $creator->addScript('packed.js');

    foreach($creator->getScriptList() as $file)
    {
      if( file_exists($file) )
        unlink($file);
    }
  }

  public function testCreateDelete()
  {
    ScriptsFileFinder::$root = dirname(__FILE__);
    $creator = new PackedScriptCreator();
    $creator->addScript('packed.js');

    $this->assertFalse($creator->scripsExists());

    $creator->create();

    $this->assertTrue($creator->scripsExists());

    $creator->delete();

    $this->assertFalse($creator->scripsExists());
  }

  public function testUpdate()
  {
    ScriptsFileFinder::$root = dirname(__FILE__);
    $creator = new PackedScriptCreator();
    $creator->addScript('packed.js');

    $this->assertFalse($creator->scripsExists());

    $creator->update();
    $this->assertTrue($creator->scripsExists());
  }

  public function testScriptsPath()
  {
    $creator = new PackedScriptCreator();
    $creator->addScript('packed.js');
    $creator->addScript('compiled.js');
    $paths = array();

    foreach( PackedScriptCreator::$scripts as $script )
    {
      $paths[] = ScriptsFileFinder::$root . $creator->path . $script;
    }

    $this->assertEquals($paths, $creator->getScriptList());
  }
}
