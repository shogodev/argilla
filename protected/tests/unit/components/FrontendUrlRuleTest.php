<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
Yii::import('frontend.components.url.FUrlManager');
Yii::$classMap['FUrlRule'] = Yii::getPathOfAlias('frontend.components.url.FUrlManager').'.php';

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