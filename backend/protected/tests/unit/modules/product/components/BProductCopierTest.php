<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
yii::import('backend.models.*');

class BProductCopierTest extends CDbTestCase
{
  protected $path;

  protected $fixtures = array(
    'product' => 'BProduct',
    'product_assignment' => 'BProductAssignment',
    'association' => 'BAssociation',
  );

  protected function setUp()
  {
    parent::setUp();

    $this->path = GlobalConfig::instance()->rootPath.'/f/product/';

    file_put_contents($this->path.'/test_copy_file.test', 'test_data1');
    file_put_contents($this->path.'/pre_test_copy_file.test', 'test_data2');
  }

  public function testCopy()
  {
    $copier = new BProductCopier(15);
    $copyId = $copier->copy();

    $model = BProduct::model()->findByPk($copyId);

    $this->assertNotNull($model);
    $this->assertEquals(444, Arr::reset($model->assignment)->section_id);
    $this->assertEquals(150, Arr::reset($model->associations)->dst_id);
  }

  public function testCopyWithImages()
  {
    $this->assertFileExists($this->path.'/test_copy_file.test');
    $this->assertFileExists($this->path.'/pre_test_copy_file.test');

    $copier = new BProductCopier(20);
    $copyId = $copier->copy(true);

    $model = BProduct::model()->findByPk($copyId);
    $this->assertEquals('Новый товар20', $model->name);

    $imageModel = BProductImg::model()->findByAttributes(array('parent' => $model->id));

    $this->assertEquals($imageModel->notice, 'test_text');
    $this->assertNotEquals($imageModel->name, 'test_copy_file.test');
    $this->assertFileExists($this->path.'/'.$imageModel->name);
    $this->assertFileExists($this->path.'/pre_'.$imageModel->name);

    unlink($this->path.'/'.$imageModel->name);
    unlink($this->path.'/pre_'.$imageModel->name);
  }

  public function tearDown()
  {
    unlink($this->path.'/test_copy_file.test');
    unlink($this->path.'/pre_test_copy_file.test');
  }
}