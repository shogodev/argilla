<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
class UploadHelperTest extends CTestCase
{
  public function setUp()
  {
    file_put_contents($this->getTestFileName(), 'test', FILE_APPEND);
  }

  public function testDoCustomFilename()
  {
    $result = UploadHelper::doUniqueFilename($this->getTestFileName());
    $this->assertNotEquals($result, $this->getTestFileName());
  }

  public function testPrepareFileName()
  {
    $this->assertEquals("testovyjfajl_.jpg", UploadHelper::prepareFileName('f/test/', 'тестовыйФайл"\\.JPG'));
    $this->assertNotEquals("test_upload_helper.jpg", UploadHelper::prepareFileName(realpath(Yii::getPathOfAlias('frontend').'/../f/product').'/', 'test_upload_helper.jpg'));
  }

  public function tearDown()
  {
    unlink($this->getTestFileName());
  }

  private function getTestFileName()
  {
    return Yii::getPathOfAlias('frontend').'/../f/product/test_upload_helper.jpg';
  }
}