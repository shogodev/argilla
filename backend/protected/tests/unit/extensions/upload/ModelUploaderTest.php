<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 27.08.12
 */

Yii::import('backend.modules.news.*');
Yii::import('backend.modules.news.models.*');
Yii::import('backend.modules.news.controllers.*');

class ModelUploaderTest extends CDbTestCase
{
  /**
   * @var UploadBehavior
   */
  public $behavior;

  public $fixtures = array('news_section' => 'NewsSection');

  public function setUp()
  {
    parent::setUp();
    Yii::app()->setUnitEnvironment('News', 'BNewsSection', 'index');

    $model = BNewsSection::model()->findByPk('1');
    $model->attachBehavior('UploadBehavior', new UploadBehavior);

    $this->behavior            = $model->asa('UploadBehavior');
    $this->behavior->attribute = 'img';
  }

  public function testSaveFile()
  {
    $section = BNewsSection::model()->findByPk('1');
    $section->img = null;
    $section->save();

    $uploader = new ModelUploader($this->behavior);
    $uploader->saveFile(array('name' => 'test_image'));

    $section->refresh();
    $this->assertEquals('test_image', $section->img);
  }

  public function testGetFiles()
  {
    $section = BNewsSection::model()->findByPk('1');
    $section->img = 'test_image';
    $section->save();

    $uploader = new ModelUploader($this->behavior);
    $files    = $uploader->getFiles();

    $this->arrayHasKey('test_image', $files);
  }

  public function testGetFileName()
  {
    $section = BNewsSection::model()->findByPk('1');
    $section->img = 'test_image';
    $section->save();

    $uploader = new ModelUploader($this->behavior);
    $file     = $uploader->getFileName(1);

    $this->arrayHasKey('test_image', $file);
  }

  public function testDeleteFile()
  {
    $section = BNewsSection::model()->findByPk('1');
    $section->img = 'test_image';
    $section->save();

    $uploader = new ModelUploader($this->behavior);
    $uploader->deleteFile(1);

    $section->refresh();
    $this->assertNull($section->img);
  }
}

?>