<?php
Yii::import('ext.mainscript.creators.*');
Yii::import('ext.mainscript.helpers.*');

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date 26.09.12
 * @package mainscript
 */
class ScriptHashHelperTest extends CTestCase
{
  public static function tearDownAfterClass()
  {
    ScriptsFileFinder::$root = dirname(__FILE__);

    $file = ScriptsFileFinder::$root . ScriptHashHelper::getInstance()->versionFile;


    if( file_exists($file) )
      unlink(ScriptsFileFinder::$root . ScriptHashHelper::getInstance()->versionFile);
  }

  public function testVersionFileNotExist()
  {
    ScriptsFileFinder::$root = dirname(__FILE__);

    $this->assertFalse(file_exists(ScriptHashHelper::getInstance()->version));
    $this->assertNotEquals(ScriptHashHelper::getInstance()->version, ScriptHashHelper::getInstance()->hash);
  }

  public function testVersionFileContent()
  {
    ScriptsFileFinder::$root = dirname(__FILE__);

    $version = null;

    if( file_exists(ScriptsFileFinder::$root . ScriptHashHelper::getInstance()->versionFile) )
      $version = file_get_contents( ScriptsFileFinder::$root . ScriptHashHelper::getInstance()->versionFile );

    $this->assertEquals(ScriptHashHelper::getInstance()->hash, (string) $version);
  }
}