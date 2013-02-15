<?php
Yii::import('ext.mainscript.helpers.*');

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date 26.09.12
 * @package mainscript
 */
class ScriptsFileFinderTest extends CTestCase
{
  public function testGetFiles()
  {
    ScriptsFileFinder::$root = dirname(__FILE__);

    $instance = ScriptsFileFinder::getInstance();

    $this->assertNotEmpty($instance->getFiles());
  }

  public function testFileExistence()
  {
    ScriptsFileFinder::$root = dirname(__FILE__);

    $instance = ScriptsFileFinder::getInstance();

    foreach( $instance->getFiles() as $file )
    {
      $this->assertTrue(file_exists($file));
    }
  }
}
