<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ProductImageTest extends CDbTestCase
{
  protected $fixtures = array(
    'product_img' => 'ProductImage',
  );

  public function testMagicGet()
  {
    /**
     * @var ProductImage $image
     */
    $image = ProductImage::model()->findByPk(1);
    $image->setImageDir(dirname(__FILE__).'/../../../fixtures/files/');

    $this->assertStringStartsWith('/', $image->__toString());
    $this->assertStringStartsWith('/', $image->__get('pre'));

    $this->assertStringEndsWith('img.png', $image->__toString());
    $this->assertStringEndsWith('pre_img.png', $image->__get('pre'));
  }

  public function testDefaultImage()
  {
    /**
     * @var ProductImage $image
     */
    $image = ProductImage::model()->findByPk(1);
    $this->assertEquals('/i/sp.gif', $image->__toString());
    $this->assertEquals('/i/sp.gif', $image->__get('pre'));
  }

  /**
   * @expectedException CException
   */
  public function testWrongThumb()
  {
    /**
     * @var ProductImage $image
     */
    $image = ProductImage::model()->findByPk(1);
    $image->__get('somePrefix');
  }
}