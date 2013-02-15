<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 28.08.12
 */
class UploadBehaviorTest extends CDbTestCase
{
  public $fixtures = array('news_section' => 'BNewsSection');

  public function setUp()
  {
    parent::setUp();
    Yii::app()->setUnitEnvironment('BInfo', 'BInfo', 'update', array('id' => '3'));
  }

  public function testBeforeDelete()
  {
    $model = BInfo::model()->findByPk('3');
    $file  = array('name' => 'img.jpg');
    $model->asa('uploadBehavior')->attribute = 'info_files';
    $model->saveUploadedFile($file);
    $model->deleteNode();

    $files = $model->getUploadedFiles();
    $this->assertEquals(0, $files->ItemCount);
  }
}

?>