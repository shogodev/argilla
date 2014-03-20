<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class DBRuleTest extends CDbTestCase
{
  protected $fixtures = array(
    'product' => 'Product',
  );

  protected function setUp()
  {
    Yii::import('frontend.components.url.FUrlRule');
    Yii::$classMap['CUrlRule'] = Yii::getPathOfAlias('system.web.CUrlManager').'.php';

    parent::setUp();
  }


  public function testParseUrl()
  {
    $route = array(
      'product/one',
      'pattern' => '<url:\w+>',
      'models' => array('url' => 'Product'),
      'class' => 'DBRule',
    );

    $urlRule = new DBRule($route, 'productOne');

    $path   = 'product_21_url';
    $result = $urlRule->parseUrl(Yii::app()->urlManager, null, $path, $path);
    $this->assertEquals('product/one', $result);
  }
}