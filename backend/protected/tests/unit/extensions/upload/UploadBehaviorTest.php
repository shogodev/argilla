<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class UploadBehaviorTest extends CDbTestCase
{
  protected $fixtures = array('news_section' => 'BNewsSection');

  public function setUp()
  {
    parent::setUp();
    Yii::app()->setUnitEnvironment('Info', 'BInfo', 'update', array('id' => '3'));
  }

  public function testBeforeDelete()
  {
    $model = BInfo::model()->findByPk(3);
    $file  = array('name' => 'img.jpg');
    $model->asa('uploadBehavior')->attribute = 'info_files';
    $model->saveUploadedFile($file);
    ob_start();
    $model->deleteNode();
    ob_end_clean();

    $files = $model->getUploadedFiles();
    $this->assertEquals(0, $files->ItemCount);
  }

  public function testInit()
  {
    $model = BInfo::model()->findByPk(4);

    $behavior = new UploadBehavior();
    $behavior->attach($model);

    $property = new ReflectionProperty($behavior, 'uploader');
    $property->setAccessible(true);
    $property->getValue($behavior);

    $behavior->attribute = 'info_files';
    $method = new ReflectionMethod($behavior, 'init');
    $method->setAccessible(true);

    $method->invoke($behavior);
    $this->assertInstanceOf('TableUploader', $property->getValue($behavior));

    $behavior->attribute = 'img';
    $method = new ReflectionMethod($behavior, 'init');
    $method->setAccessible(true);

    $method->invoke($behavior);
    $this->assertInstanceOf('TreeModelUploader', $property->getValue($behavior));
  }
}