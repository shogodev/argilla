<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 30.08.12
 */

Yii::import('frontend.extensions.upload.*');
Yii::import('frontend.extensions.upload.actions.*');

class UploadActionTest extends CDbTestCase
{
  public $fixtures = array('info' => 'BInfo');

  public $actionClass;

  public $module;

  public $path;

  public function setUp()
  {
    parent::setUp();
    Yii::app()->setUnitEnvironment('BInfo', 'BInfo', 'update', array('id' => '2', 'attr' => 'info_files'));
    Yii::app()->controller->module->id = 'info_test';

    $this->actionClass = Yii::app()->controller->createAction('upload');
    $init = new ReflectionMethod('UploadAction', 'init');
    $init->setAccessible(true);
    $init->invoke($this->actionClass);

    $this->module = Yii::app()->controller->module;
    $this->module->thumbsSettings = array('info' => array('pre' => array(100, 100)));

    $this->path = $this->module->getUploadPath();
  }

  public function testUpload()
  {
    $uploadedFile = TUploadedFile::init();
    TUploadedFile::setFile(null, Yii::getPathOfAlias('backend.tests.fixtures.files').'/img.jpg');

    $uploadAction = new ReflectionMethod('UploadAction', 'uploadFile');
    $uploadAction->setAccessible(true);
    $uploadAction->invoke($this->actionClass, $uploadedFile);

    $this->assertFileExists($this->path.'img.jpg');
    $this->assertFileExists($this->path.'pre_img.jpg');

    $file = Yii::app()->db->createCommand()->from('{{info_files}}')->where('parent=:p', array(':p' => 2))->queryAll();
    $this->assertEquals('img.jpg', $file[0]['name']);

    $this->assertTrue(unlink($this->path.'img.jpg'));
    $this->assertTrue(unlink($this->path.'pre_img.jpg'));
  }

  public function testDelete()
  {
    $name     = tempnam($this->path, 'tmp');
    $pathinfo = pathinfo($name);
    $preName  = $this->path.'pre_'.$pathinfo['filename'];
    copy($name, $preName);

    Yii::app()->db->createCommand()->insert('{{info_files}}', array('name' => $pathinfo['filename']));
    $id = Yii::app()->db->getLastInsertID();

    $uploadAction = new ReflectionMethod('UploadAction', 'deleteFile');
    $uploadAction->setAccessible(true);
    $uploadAction->invoke($this->actionClass, $id);

    $this->assertFileNotExists($name);
    $this->assertFileNotExists($preName);

    $files = Yii::app()->db->createCommand()->from('{{info_files}}')->queryAll();
    $this->assertCount(0, $files);
  }

  public function tearDown()
  {
    $table = Yii::app()->db->getSchema()->getTable('{{info_files}}');
    $sql   = Yii::app()->db->getSchema()->truncateTable($table->name);
    Yii::app()->db->createCommand($sql)->execute();

    rmdir($this->path);
  }
}

?>