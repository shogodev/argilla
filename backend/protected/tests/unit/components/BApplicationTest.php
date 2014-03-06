<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BApplicationTest extends CTestCase
{
  /**
   * @var BApplication
   */
  public $app;

  public function setUp()
  {
    parent::setUp();

    $this->app = Yii::app();
  }

  public function testGetFrontendRoot()
  {
    $path = $this->app->getFrontendRoot();
    $file = realpath(__DIR__.'/../../../../..').'/';

    $this->assertEquals($path, $file);
  }

  public function testSetUnitEnvironment()
  {
    $this->app->setUnitEnvironment('News', 'BNewsSection', 'index');

    $this->assertInstanceOf('BNewsSectionController', $this->app->controller);
    $this->assertEquals('bnewssection', $this->app->controller->id);

    $this->assertInstanceOf('NewsModule', $this->app->controller->module);

    $this->assertInstanceOf('CInlineAction', $this->app->controller->action);
    $this->assertEquals('index', $this->app->controller->action->id);
  }

  /**
   * @expectedException BTestEndException
   * @expectedExceptionCode 1101
   */
  public function testEnd()
  {
    $this->app->end('1101');
  }
}