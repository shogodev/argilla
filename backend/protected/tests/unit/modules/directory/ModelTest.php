<?php

Yii::import('backend.tests.unit.modules.directory.common.*');

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date 24.10.12
 * @package Directory
 */
class ModelTest extends DirectoryTestCase
{
  public function testCreate()
  {
    $directory = new TestDirectory();
    $directory->name = 'test';
    $this->assertTrue($directory->save());
  }

  public function testCreatefail()
  {
    $directory = new TestDirectory();
    $this->assertFalse($directory->save());
  }
}