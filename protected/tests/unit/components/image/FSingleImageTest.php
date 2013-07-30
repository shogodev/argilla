<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class FSingleImageTest extends CTestCase
{
  public function testMagicGet()
  {
    $image = new FSingleImage('img.png', 'files', array('pre'), '/i/sp.gif');
    $image->setImageDir(dirname(__FILE__).'/../../../fixtures/');

    $this->assertStringStartsWith('/', $image->__toString());
    $this->assertStringStartsWith('/', $image->__get('pre'));

    $this->assertStringEndsWith('img.png', $image->__toString());
    $this->assertStringEndsWith('pre_img.png', $image->__get('pre'));
  }

  public function testDefaultImage()
  {
    $image = new FSingleImage('someFile.png', 'files', array('pre'));
    $this->assertEquals('/i/sp.gif', $image->__toString());
    $this->assertEquals('/i/sp.gif', $image->__get('pre'));
  }

  /**
   * @expectedException CException
   */
  public function testWrongThumb()
  {
    $image = new FSingleImage('someFile.png', 'files', array());
    $image->__get('pre');
  }
}