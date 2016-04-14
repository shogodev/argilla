<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2016 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('backend.modules.product.modules.import.tests.components.*');
Yii::import('backend.modules.product.modules.import.components.*');

class ImageWriterTest extends CDbTestCase
{
  protected $fixtures = array(
    'product' => 'BProduct',
    'product_img' => 'BProductImg',
  );

  private $basePath;

  private $testSourceDir;

  private $testOutDir;

  /**
   * @var ImageWriter
   */
  protected $imageWriter;

  public function setUp()
  {
    $imageWriter = new ImageWriter(new DummyConsoleFileLogger('test'), 'f/product/testSrc', 'f/product/testOutput');
    $imageWriter->previews = array(
      'origin' => array(4500, 4500),
      'big' => array(600, 460),
      'pre' => array(250, 190),
    );

    $imageWriter->uniqueAttribute = 'id';
    $imageWriter->clear = false;
    $imageWriter->clearTables = array('{{product_img}}');
    $imageWriter->defaultJpegQuality = 95;
    $imageWriter->phpThumbErrorExceptionToWarning = true;

    $this->imageWriter = $imageWriter;

    $currentDir = dirname(__FILE__);

    $this->basePath = realpath(Yii::getPathOfAlias('frontend').'/..');
    $this->testSourceDir = $this->basePath.'/f/product/testSrc';
    $this->testOutDir = $this->basePath.'/f/product/testOutput';

    CFileHelper::createDirectory($this->testSourceDir);
    CFileHelper::createDirectory($this->testOutDir);
    CFileHelper::copyDirectory($currentDir.'/files', $this->testSourceDir);

    $this->getFixtureManager()->basePath = $currentDir.'/fixtures/';

    parent::setUp();
  }

  public function testWriteNewData()
  {
    $data = array(
      1 => array(
        array(
          'file' => 'test_file01.jpg',
          'rowIndex' => '1'
        ),
        array(
          'file' => 'test_file02.jpg',
          'rowIndex' => '1'
        )
      ),
      2 => array(
        array(
          'file' => 'test_file03.jpg',
          'rowIndex' => '2'
        )
      )
    );

    $this->imageWriter->actionWithExistingRecord = ImageWriter::DB_ACTION_EXISTING_RECORD_SKIP_WITH_WARNING;
    $this->imageWriter->actionWithSameFiles = ImageWriter::FILE_ACTION_RENAME_NEW_FILE;
    $this->imageWriter->writeAll($data);

    $this->assertTrue(empty($this->imageWriter->logger->testLog['error']));
    $this->assertTrue(empty($this->imageWriter->logger->testLog['warning']));

    $this->assertNotNull(BProductImg::model()->findByAttributes(array('parent' => 1, 'name' => 'test_file01.jpg', 'type' => 'main')));
    $this->assertNotNull(BProductImg::model()->findByAttributes(array('parent' => 1, 'name' => 'test_file02.jpg', 'type' => 'gallery')));
    $this->assertNotNull(BProductImg::model()->findByAttributes(array('parent' => 2, 'name' => 'test_file03.jpg', 'type' => 'main')));

    $this->assertTrue(file_exists($this->testOutDir.'/test_file01.jpg'));
    $this->assertTrue(file_exists($this->testOutDir.'/pre_test_file01.jpg'));
    $this->assertTrue(file_exists($this->testOutDir.'/big_test_file01.jpg'));

    $this->assertTrue(file_exists($this->testOutDir.'/test_file02.jpg'));
    $this->assertTrue(file_exists($this->testOutDir.'/pre_test_file02.jpg'));
    $this->assertTrue(file_exists($this->testOutDir.'/big_test_file02.jpg'));

    $this->assertTrue(file_exists($this->testOutDir.'/test_file03.jpg'));
    $this->assertTrue(file_exists($this->testOutDir.'/pre_test_file03.jpg'));
    $this->assertTrue(file_exists($this->testOutDir.'/big_test_file03.jpg'));
  }

  public function testRenameFile()
  {
    $data = array(
      1 => array(
        array(
          'file' => 'test_file01.jpg',
          'rowIndex' => '1'
        ),
        array(
          'file' => 'test_file02.jpg',
          'rowIndex' => '1'
        )
      ),
      2 => array(
        array(
          'file' => 'test_file01.jpg',
          'rowIndex' => '2'
        )
      )
    );

    $this->imageWriter->actionWithExistingRecord = ImageWriter::DB_ACTION_EXISTING_RECORD_SKIP_SILENT;
    $this->imageWriter->actionWithSameFiles = ImageWriter::FILE_ACTION_RENAME_NEW_FILE;

    $this->assertNull(BProductImg::model()->findByAttributes(array('parent' => 2)));

    $this->imageWriter->writeAll($data);

    $this->assertTrue(empty($this->imageWriter->logger->testLog['error']));
    $this->assertTrue(empty($this->imageWriter->logger->testLog['warning']));

    $this->assertTrue(file_exists($this->testOutDir.'/test_file01.jpg'));
    $this->assertTrue(file_exists($this->testOutDir.'/pre_test_file01.jpg'));
    $this->assertTrue(file_exists($this->testOutDir.'/big_test_file01.jpg'));

    $this->assertTrue(file_exists($this->testOutDir.'/test_file02.jpg'));
    $this->assertTrue(file_exists($this->testOutDir.'/pre_test_file02.jpg'));
    $this->assertTrue(file_exists($this->testOutDir.'/big_test_file02.jpg'));

    $this->assertNotNull(BProductImg::model()->findByAttributes(array('parent' => 1, 'name' => 'test_file01.jpg', 'type' => 'main')));
    $this->assertNotNull(BProductImg::model()->findByAttributes(array('parent' => 1, 'name' => 'test_file02.jpg', 'type' => 'gallery')));

    $record = BProductImg::model()->findByAttributes(array('parent' => 2));

    $this->assertNotNull($record);

    $fileName = $record['name'];

    $this->assertTrue(file_exists($this->testOutDir.'/'.$fileName));
    $this->assertTrue(file_exists($this->testOutDir.'/pre_'.$fileName));
    $this->assertTrue(file_exists($this->testOutDir.'/big_'.$fileName));
  }

  /*  public function testUpdateDbRecord()
  {
  }*/

  public function tearDown()
  {
    CFileHelper::removeDirectory($this->testSourceDir);
    CFileHelper::removeDirectory($this->testOutDir);

    parent::tearDown();
  }
}