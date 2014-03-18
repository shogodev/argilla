<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class FUrlRuleTest extends CTestCase
{
  public function testParseUrl()
  {
    $route = array(
      'news/section',
      'pattern'       => '<url:(news|articles)>/<page:\d*>',
      'defaultParams' => array('page' => 1)
    );

    $urlRule = new FUrlRule($route, 'newsSection');

    $path   = 'news/1';
    $result = $urlRule->parseUrl(Yii::app()->urlManager, null, $path, $path);
    $this->assertTrue($result !== false);

    $path   = 'articles';
    $result = $urlRule->parseUrl(Yii::app()->urlManager, null, $path, $path);
    $this->assertTrue($result !== false);

    $path   = 'news/1/1';
    $result = $urlRule->parseUrl(Yii::app()->urlManager, null, $path, $path);
    $this->assertFalse($result);
  }

  public function testParseUrlWithDefaultParams()
  {
    $route = array(
      'product/section',
      'pattern' => 'section/<section:\w+>/<page:\d+>',
      'defaultParams' => array('page' => 1)
    );

    $urlRule = new FUrlRule($route, 'productSection');

    $path   = 'section/some_section';
    $result = $urlRule->parseUrl(Yii::app()->urlManager, null, $path, $path);
    $this->assertTrue($result !== false);
    $this->assertTrue(Yii::app()->urlManager->defaultParamsUsed);

    $path   = 'section/some_section/2';
    $result = $urlRule->parseUrl(Yii::app()->urlManager, null, $path, $path);
    $this->assertTrue($result !== false);
    $this->assertFalse(Yii::app()->urlManager->defaultParamsUsed);
  }

  public function testCreateUrl()
  {
    $route = array(
      'product/section',
      'pattern' => 'section/<section:\w+>/<page:\d+>',
      'defaultParams' => array('page' => 1)
    );

    $urlRule = new FUrlRule($route, 'productSection');

    $result = $urlRule->createUrl(Yii::app()->urlManager, 'product/section', array('section' => 'product_section'), '&');
    $this->assertEquals('section/product_section/', $result);

    $result = $urlRule->createUrl(Yii::app()->urlManager, 'product/section', array('section' => 'product_section', 'page' => 2), '&');
    $this->assertEquals('section/product_section/2/', $result);

    $route = array('info/index', 'pattern' => '<url:\w+>');
    $urlRule = new FUrlRule($route, 'productSection');

    $result = $urlRule->createUrl(Yii::app()->urlManager, 'info/index', array('url' => 'info'), '&');
    $this->assertEquals('info/', $result);

    $result = $urlRule->createUrl(Yii::app()->urlManager, 'info/index', array('url' => 'info', 'parameter' => 'value'), '&');
    $this->assertEquals('info/?parameter=value', $result);
  }
}