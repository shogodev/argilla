<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.unit.components
 */
class FControllerTest extends CTestCase
{
  public function testGetActionParams()
  {
    $_GET['page'] = 10;
    $_GET['param'] = 'value';

    $controller = new FController('index');
    Yii::app()->urlManager->addRules(array(
      'search' => array('search/index', 'pattern' => 'search/<page:\d+>', 'defaultParams' => array('page' => 1)),
    ));
    Yii::app()->urlManager->ruleIndex = 'search';
    $params = $controller->getActionParams(true);

    $this->assertEquals('value', $params['param']);
    $this->assertArrayNotHasKey('page', $params);
  }
}