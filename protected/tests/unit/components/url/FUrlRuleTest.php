<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.unit.components.url
 */

Yii::import('frontend.components.url.FUrlManager');
Yii::$classMap['FUrlRule'] = Yii::getPathOfAlias('frontend.components.url.FUrlManager').'.php';

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
}