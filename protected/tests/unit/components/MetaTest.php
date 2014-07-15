<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.unit.components
 */
class MetaTest extends CDbTestCase
{
  protected $fixtures = array(
    'seo_meta_route' => 'MetaRoute',
    'seo_meta_mask' => 'MetaMask',
    'product_section' => 'ProductSection',
  );

  public function testInit()
  {
    Yii::app()->setUnitEnvironment('index', 'index');

    $meta = new Meta();
    $meta->init();

    $renderHandler = Yii::app()->controller->getEventHandlers('onBeforeRender')[0];
    $this->assertInstanceOf('Meta', $renderHandler[0]);
    $this->assertEquals('setRenderedModels', $renderHandler[1]);

    $endHandler = Yii::app()->getEventHandlers('onEndRequest')[0];
    $this->assertInstanceOf('Meta', $endHandler[0]);
    $this->assertEquals('updateRenderedModels', $endHandler[1]);
  }

  public function testDefaultMeta()
  {
    Yii::app()->setUnitEnvironment('index', 'testAction');

    $meta = new Meta();
    $meta->setController(Yii::app()->controller);
    $meta->setMeta();

    $this->assertEquals('defaultTitle', $meta->getTitle());
    $this->assertEquals('defaultDescription', $meta->getDescription());
    $this->assertEquals('defaultKeywords', $meta->getKeywords());
  }

  public function testMaskTitle()
  {
    $meta = new Meta();
    $meta->setRequestUri('/testUri/');
    $meta->setMeta();

    $this->assertEquals('mask page title', $meta->getTitle());
  }

  public function testRouteTitle()
  {
    Yii::app()->setUnitEnvironment('index', 'index');

    $meta = new Meta();
    $meta->setController(Yii::app()->controller);
    $meta->setMeta();

    $this->assertEquals('indexTitle', $meta->getTitle());
  }

  public function testMetaOverriding()
  {
    Yii::app()->setUnitEnvironment('index', 'index');

    $meta = new Meta();
    $meta->setController(Yii::app()->controller);
    $meta->setMeta();
    $this->assertEquals('indexTitle', $meta->getTitle());

    $meta->setRequestUri('/testOverriding/');
    $meta->setMeta();
    $this->assertEquals('Overrated title', $meta->getTitle());
  }

  public function testReplaceCommands()
  {
    $meta = new Meta();
    $section = ProductSection::model()->findByPk(8);
    $meta->addModels(array($section, new stdClass()));

    $meta->setRequestUri('/testVars/');
    $meta->setMeta();
    $this->assertEquals('section Name', $meta->getTitle());

    $meta->setRequestUri('/testCommandsUpper/');
    $meta->setMeta();
    $this->assertEquals('SECTION NAME', $meta->getTitle());

    $meta->setRequestUri('/testCommandsLower/');
    $meta->setMeta();
    $this->assertEquals('section name', $meta->getTitle());

    $meta->setRequestUri('/testCommandsUcfirst/');
    $meta->setMeta();
    $this->assertEquals('Section Name', $meta->getTitle());
  }

  public function testReplaceCommandsInClips()
  {
    $meta = new Meta();

    $meta->setRequestUri('/testCommandsWrap/');
    $meta->setMeta();
    $meta->registerClip('color', 'white');
    $this->assertEquals('/white/', $meta->getTitle());
  }

  public function testEmptyWrap()
  {
    $meta = new Meta();

    $meta->setRequestUri('/testCommandsEmptyWrap/');
    $meta->setMeta();
    $meta->registerClip('color', 'white');
    $this->assertEquals('(white)', $meta->getTitle());
  }

  public function testMultiCommands()
  {
    $meta = new Meta();
    $meta->addModels(array(ProductSection::model()->findByPk(8)));
    $meta->setRequestUri('/testMultiCommands/');
    $meta->setMeta();
    $meta->registerClip('color', 'white');
    $this->assertEquals('/white section Name/', $meta->getTitle());
  }

  public function testWrongCommand()
  {
    $meta = new Meta();

    $meta->setRequestUri('/testWrongCommand/');
    $meta->setMeta();
    $this->assertEquals('', $meta->getTitle());
  }

  public function testUrlWithGet()
  {
    $meta = new Meta();

    $meta->setRequestUri('/test_with_get/?param=value');
    $meta->setMeta();
    $this->assertEquals('page title', $meta->getTitle());
  }

  public function testSetHeader()
  {
    Yii::app()->setUnitEnvironment('index', 'index');

    $meta = new Meta();
    $meta->setMeta();
    $meta->setHeader('indexHeader');
    $this->assertEquals('indexHeader defaultTitle', $meta->getTitle());
  }

  public function testUpdateRenderedModels()
  {
    Yii::app()->setUnitEnvironment('index', 'newAction');

    $meta = new Meta();
    $meta->addModels(array(ProductSection::model()->findByPk(8)));
    $meta->registerClip('templateVar', 'var');
    $meta->setController(Yii::app()->controller);
    $meta->setMeta();
    $meta->updateRenderedModels();

    /**
     * @var MetaRoute $metaRoute
     */
    $metaRoute = MetaRoute::model()->resetScope()->findByAttributes(array('route' => 'index/newAction'));
    $this->assertEquals('ProductSection', $metaRoute->models);
    $this->assertEquals('templateVar', $metaRoute->clips);
  }

  public function testRegisterMeta()
  {
    Yii::app()->setUnitEnvironment('index', 'noindex');

    $meta = new Meta();
    $meta->setController(Yii::app()->controller);
    $meta->setRenderedModels(new CEvent());
    $meta->registerMeta();

    Yii::app()->clientScript;

    $reflection = new ReflectionProperty(Yii::app()->clientScript, 'metaTags');
    $reflection->setAccessible(true);
    $values = $reflection->getValue(Yii::app()->clientScript);

    $this->assertEquals('defaultKeywords', $values['keywords']['content']);
    $this->assertEquals('defaultDescription', $values['description']['content']);
    $this->assertEquals('noindex, nofollow', $values['robots']['content']);
  }
}