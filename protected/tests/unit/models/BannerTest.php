<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class BannerTest extends CDbTestCase
{
  protected $fixtures = array('banner' => 'Banner');

  /**
   * @var Banner
   */
  private $banner;

  /**
   * @var FController
   */
  private $controller;

  public function setUp()
  {
    parent::setUp();

    $this->banner = Banner::model();

    $this->controller = Arr::get(Yii::app()->createController('info'), 0);
    $action = new CViewAction($this->controller, 'index');
    $this->controller->setAction($action);
    Yii::app()->setController($this->controller);
  }

  public function testGetByLocationAll()
  {
    $banners = $this->banner->getByLocationAll('left');

    $this->assertEquals($banners[0]->id, 1);
    $this->assertEquals($banners[1]->id, 3);
  }

  public function testGetByLocation()
  {
    $banner = $this->banner->getByLocation('left');

    $this->assertEquals($banner->id, 1);
  }

  public function testGetByCurrentUrlAll()
  {
    $banners = $this->banner->getByCurrentUrlAll();

    $this->assertEquals($banners[0]->id, 4);
    $this->assertEquals($banners[1]->id, 5);
    $this->assertEquals($banners[2]->id, 6);
    $this->assertCount(3, $banners);

    $banners = $this->banner->getByCurrentUrlAll('right');
    $this->assertEquals($banners[0]->id, 5);
  }

  public function testGetByCurrentUrl()
  {
    $banner = $this->banner->getByCurrentUrl();

    $this->assertEquals($banner->id, 4);
  }

  public function testGetPrepareUrl()
  {
    $class = new ReflectionClass($this->banner);
    $method = $class->getMethod('getPrepareUrl');
    $method->setAccessible(true);
    $this->assertEquals($method->invoke($this->banner, '/news/?page=2'), '/news/');
    $this->assertEquals($method->invoke($this->banner, '/news/?page=2&test=4'), '/news/');
    $this->assertEquals($method->invoke($this->banner, '/news/?page'), '/news/');
  }

  public function testContainUrl()
  {
    $class = new ReflectionClass($this->banner);
    $method = $class->getMethod('containUrl');
    $method->setAccessible(true);

    $this->assertFalse($method->invoke($this->banner, '/news/?page=2', '/news/'));
    $this->assertTrue($method->invoke($this->banner, '*', 'any'));

    $data = "/news/\n\r/info/";
    $this->assertTrue($method->invoke($this->banner, $data, '/news/'));
    $this->assertTrue($method->invoke($this->banner, $data, '/info/'));

    $this->assertTrue($method->invoke($this->banner, "/news/*", '/news/1/'));

    $this->assertFalse($method->invoke($this->banner, "/news/*", 'section/news/'));
  }
}