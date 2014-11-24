<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BControllerTest extends CDbTestCase
{
  /**
   * @var BController
   */
  public $controller;

  protected $fixtures = array(
    'news_section' => 'BNewsSection',
    'news' => 'BNews',
  );

  public function setUp()
  {
    parent::setUp();

    /**
     * @var BController
     */
    $this->controller = $this->getMockForAbstractClass('BController', array('secure'));
    $this->controller->modelClass = 'BNews';

    Yii::app()->setUnitEnvironment('News', 'BNews', 'update', array('id' => '1'));
    ob_start();
  }

  /**
   * @expectedException BTestRedirectException
   * @expectedExceptionMessage Location: backend/base
   */
  public function testBeforeAction()
  {
    $action = new CInlineAction($this->controller, 'index');
    $result = $this->controller->beforeAction($action);
    $url_index = Yii::app()->user->getState('secure');

    $this->assertTrue($result);
    $this->assertNotEmpty($url_index);

    $action = new CInlineAction($this->controller, 'update');
    $result = $this->controller->beforeAction($action);
    $url_update = Yii::app()->user->getState('secure');

    $this->assertTrue($result);
    $this->assertEquals($url_update, $url_index);
  }

  public function testGetBackUrl()
  {
    Yii::app()->user->setState($this->controller->uniqueId, '1');

    $url = $this->controller->getBackUrl();
    $this->assertEquals($url, '1');
  }

  public function testActions()
  {
    $actions = $this->controller->actions();

    $this->assertArrayHasKey('delete', $actions);
    $this->assertArrayHasKey('toggle', $actions);
    $this->assertArrayHasKey('switch', $actions);
  }

  public function testLoadModel()
  {
    $model = $this->controller->loadModel('1');
    $this->assertInstanceOf('BNews', $model);
  }

  /**
   * @expectedException BTestRedirectException
   * @expectedExceptionMessage Location: backend/secure/update/1
   */
  public function testSaveModel()
  {
    $name = 'testSaveModel';
    $model = new BNews;
    $_POST['BNews'] = array('name' => $name, 'url' => 'testUrl1', 'section_id' => '1');

    $method = new ReflectionMethod('BController', 'saveModel');
    $method->setAccessible(true);
    $method->invoke($this->controller, $model);
  }

  /**
   * @expectedException BTestRedirectException
   * @expectedExceptionMessage Location: backend/secure/update/1
   */
  public function testSaveModels()
  {
    $login = 'testLogin1';
    $name = 'testName1';

    $userModel = new BFrontendUser();
    $dataModel = new BUserProfile();

    $_POST['BFrontendUser'] = array('email' => '123@123.ru', 'login' => $login);
    $_POST['BUserProfile'] = array('name' => $name);
    $_SERVER['REQUEST_METHOD'] = 'POST';

    $method = new ReflectionMethod('BController', 'saveModels');
    $method->setAccessible(true);
    $method->invoke($this->controller, array($userModel, $dataModel));
  }

  public function testValidateModels()
  {
    $productModel = new BProduct;
    $assignmentModel = new BProductAssignment;

    $method = new ReflectionMethod('BController', 'validateModels');
    $method->setAccessible(true);

    $_POST['BProduct'] = array('name' => 'testValidateModels', 'url' => 'testUrl3', 'articul' => 'articul3', 'section_id' => '1103');
    $_POST['BProductAssignment'] = array_fill_keys(array_keys($assignmentModel->getFields()), '1103');

    $result = $method->invoke($this->controller, array($productModel, $assignmentModel));
    $this->assertTrue($result);

    $_POST['BProduct'] = array('name' => 'testValidateModels');

    $result = $method->invoke($this->controller, array(new BProduct));
    $this->assertFalse($result);
  }

  /**
   * @expectedException BTestEndException
   */
  public function testPerformAjaxValidationForSeveralModels()
  {
    $name = 'testPerformAjaxValidationForSeveralModels';
    $productModel = new BProduct;
    $assignmentModel = new BProductAssignment;

    $_POST['ajax'] = $productModel->getFormId();
    $_POST['BProduct'] = array('name' => $name, 'url' => 'testUrl4', 'articul' => 'articul4', 'section_id' => '1104');
    $_POST['BProductAssignment'] = array_fill_keys(array_keys($assignmentModel->getFields()), '1104');

    $method = new ReflectionMethod('BController', 'performAjaxValidationForSeveralModels');
    $method->setAccessible(true);

    $method->invoke($this->controller, array($productModel, $assignmentModel));
    $this->expectOutputString('[]');
  }

  /**
   * @expectedException BTestEndException
   */
  public function testErrorPerformAjaxValidationForSeveralModels()
  {
    $name = 'testPerformAjaxValidationForSeveralModels';
    $productModel = new BProduct;

    $_POST['ajax'] = $productModel->getFormId();
    $_POST['BProduct'] = array('name' => $name);

    $method = new ReflectionMethod('BController', 'performAjaxValidationForSeveralModels');
    $method->setAccessible(true);

    $method->invoke($this->controller, array($productModel));
    $this->assertNotEquals('[]', $this->getActualOutput());
  }

  /**
   * @expectedException BTestEndException
   */
  public function testPerformAjaxValidation()
  {
    $name = 'testPerformAjaxValidation';
    $productModel = new BProduct;
    $_POST['ajax'] = $productModel->getFormId();
    $_POST['BProduct'] = array('name' => $name, 'url' => 'testUrl4', 'articul' => 'articul4', 'section_id' => 1);

    $method = new ReflectionMethod('BController', 'performAjaxValidation');
    $method->setAccessible(true);

    $method->invoke($this->controller, $productModel);
    $this->expectOutputString('[]');
  }

  /**
   * @expectedException BTestEndException
   */
  public function testErrorPerformAjaxValidation()
  {
    $name = 'testPerformAjaxValidation';
    $productModel = new BProduct;
    $_POST['ajax'] = $productModel->getFormId();
    $_POST['BProduct'] = array('name' => $name);

    $method = new ReflectionMethod('BController', 'performAjaxValidation');
    $method->setAccessible(true);

    $method->invoke($this->controller, $productModel);
    $this->assertNotEquals('[]', $this->getActualOutput());
  }

  /**
   * @expectedException BTestRedirectException
   * @expectedExceptionMessage Location: testUrl
   */
  public function testRedirect()
  {
    $this->controller->redirect('testUrl');
  }

  public function testActionIndex()
  {
    $this->controller->modelClass = 'BNews';
    $this->controller->actionIndex();

    $this->assertArrayHasKey('dataProvider', Yii::app()->user->getFlash('render'));
  }

  public function tearDown()
  {
    Yii::app()->db->createCommand()->truncateTable('{{user_profile}}');
    Yii::app()->db->createCommand()->truncateTable('{{user}}');
    ob_end_clean();
  }
}