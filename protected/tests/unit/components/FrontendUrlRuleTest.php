<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 24.09.12
 */

Yii::import('frontend.components.FUrlManager');
Yii::$classMap['FUrlRule'] = Yii::getPathOfAlias('frontend.components.FUrlManager').'.php';

class FrontendUrlRuleTest extends CTestCase
{
  public function testParseUrl()
  {
    $pattern = 'newsSection';
    $route   = array('news/section',
                     'pattern'       => '<url:(news|articles)>/<page:\d*>',
                     'defaultParams' => array('page' => 1));

    $urlRule = new FUrlRule($route, $pattern);

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