<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 28.08.12
 */
class TableUploaderTest extends CDbTestCase
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

    $this->behavior        = $model->asa('UploadBehavior');
    $this->behavior->table = Yii::app()->db->tablePrefix.'news_section_img';
  }

  public function testSaveFile()
  {
    $file     = array('name' => 'test_name');
    $uploader = new TableUploader($this->behavior);
    $uploader->saveFile($file);

    $id    = Yii::app()->db->getLastInsertID();
    $files = Yii::app()->db->createCommand()->from($this->behavior->table)
                                            ->where('id='.$id)
                                            ->queryAll();

    $this->assertEquals('test_name', $files[0]['name']);
  }

  public function testGetFiles()
  {
    $uploader = new TableUploader($this->behavior);

    $file1 = array('name' => 'test_name1');
    $file2 = array('name' => 'test_name2');

    $uploader->saveFile($file1);
    $uploader->saveFile($file2);
    $files = $uploader->getFiles();

    $this->assertEquals(2, $files->itemCount);
  }

  public function testGetFileName()
  {
    $file     = array('name' => 'test_name3');
    $uploader = new TableUploader($this->behavior);
    $uploader->saveFile($file);

    $id   = Yii::app()->db->getLastInsertID();
    $file = $uploader->getFileName($id);
    $this->assertEquals('test_name3', $file);
  }

  public function testDeleteFile()
  {
    $file     = array('name' => 'test_name3');
    $uploader = new TableUploader($this->behavior);
    $uploader->saveFile($file);

    $id = Yii::app()->db->lastInsertID;
    $uploader->deleteFile($id);
    $file = $uploader->getFileName($id);

    $this->assertNull($file);
  }

  public function tearDown()
  {
    $this->getFixtureManager()->truncateTable($this->behavior->table);
  }
}

?>