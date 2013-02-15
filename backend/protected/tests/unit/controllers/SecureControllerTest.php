<?php
/**
 * User: glagolev
 * Date: 31.07.12
 */
class SecureControllerTest extends CDbTestCase
{
  /**
   * @var BController
   */
  public $controller;

  public $fixtures = array('news_section' => 'NewsSection', 'news' => 'News', 'currency' => 'ProductCurrency');

  public function setUp()
  {
    parent::setUp();

    /**
     * @var BController
     */
    $this->controller = $this->getMockForAbstractClass('BController', array('secure'));
    $this->controller->modelClass = 'BNews';

    Yii::app()->setUnitEnvironment('News', 'BNews', 'update', array('id' => '1'));
  }

  public function testBeforeAction()
  {
    $action     = new CInlineAction($this->controller, 'index');
    $result     = $this->controller->beforeAction($action);
    $url_index  = Yii::app()->user->getState('secure');

    $this->assertTrue($result);
    $this->assertNotEmpty($url_index);

    $action     = new CInlineAction($this->controller, 'update');
    $result     = $this->controller->beforeAction($action);
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

  public function testSaveModel()
  {
    $name             = 'testSaveModel';
    $productModel     = new BProduct;
    $_POST['BProduct'] = array('name' => $name, 'url' => 'testUrl1', 'articul' => 'articul1');

    $method = new ReflectionMethod('BController', 'saveModel');
    $method->setAccessible(true);
    $method->invoke($this->controller, $productModel);

    $product = BProduct::model()->find('name=:n', array(':n' => $name));

    $this->assertEquals($name, $product->name);
  }

  public function testSaveModels()
  {
    $name            = 'testSaveModels';
    $productModel    = new BProduct;
    $assignmentModel = new BProductAssignment;

    $_POST['BProduct']           = array('name' => $name, 'url' => 'testUrl2', 'articul' => 'articul2');
    $_POST['BProductAssignment'] = array_fill_keys($assignmentModel->assignmentFields, '1102');
    $_SERVER['REQUEST_METHOD']  = 'POST';

    $method = new ReflectionMethod('BController', 'saveModels');
    $method->setAccessible(true);
    $method->invoke($this->controller, array($productModel, $assignmentModel));

    $product = BProduct::model()->find('name=:n', array(':n' => $name));

    $this->assertEquals($name, $product->name);
    $this->assertEquals('1102', $product->assignment->section_id);
  }

  public function testValidateModels()
  {
    $productModel     = new BProduct;
    $assignmentModel  = new BProductAssignment;

    $method = new ReflectionMethod('BController', 'validateModels');
    $method->setAccessible(true);

    $_POST['BProduct']           = array('name' => 'testValidateModels', 'url' => 'testUrl3', 'articul' => 'articul3');
    $_POST['BProductAssignment'] = array_fill_keys($assignmentModel->assignmentFields, '1103');

    $result = $method->invoke($this->controller, array($productModel, $assignmentModel));
    $this->assertTrue($result);

    $_POST['BProduct'] = array('name' => 'testValidateModels');

    $result = $method->invoke($this->controller, array(new BProduct));
    $this->assertFalse($result);
  }

  public function testPerformAjaxValidationForSeveralModels()
  {
    $name            = 'testPerformAjaxValidationForSeveralModels';
    $productModel    = new BProduct;
    $assignmentModel = new BProductAssignment;

    $_POST['ajax']              = $productModel->getFormId();
    $_POST['BProduct']           = array('name' => $name, 'url' => 'testUrl4', 'articul' => 'articul4');
    $_POST['BProductAssignment'] = array_fill_keys($assignmentModel->assignmentFields, '1104');

    $method = new ReflectionMethod('BController', 'performAjaxValidationForSeveralModels');
    $method->setAccessible(true);

    $method->invoke($this->controller, array($productModel, $assignmentModel));
    $this->expectOutputString('[]');
  }

  public function testErrorPerformAjaxValidationForSeveralModels()
  {
    $name             = 'testPerformAjaxValidationForSeveralModels';
    $productModel     = new BProduct;

    $_POST['ajax']    = $productModel->getFormId();
    $_POST['BProduct'] = array('name' => $name);

    $method = new ReflectionMethod('BController', 'performAjaxValidationForSeveralModels');
    $method->setAccessible(true);

    $method->invoke($this->controller, array($productModel));
    $this->assertNotEquals('[]', $this->getActualOutput());
  }

  public function testPerformAjaxValidation()
  {
    $name             = 'testPerformAjaxValidation';
    $productModel     = new BProduct;
    $_POST['ajax']    = $productModel->getFormId();
    $_POST['BProduct'] = array('name' => $name, 'url' => 'testUrl4', 'articul' => 'articul4');

    $method = new ReflectionMethod('BController', 'performAjaxValidation');
    $method->setAccessible(true);

    $method->invoke($this->controller, $productModel);
    $this->expectOutputString('[]');
  }

  public function testErrorPerformAjaxValidation()
  {
    $name             = 'testPerformAjaxValidation';
    $productModel     = new BProduct;
    $_POST['ajax']    = $productModel->getFormId();
    $_POST['BProduct'] = array('name' => $name);

    $method = new ReflectionMethod('BController', 'performAjaxValidation');
    $method->setAccessible(true);

    $method->invoke($this->controller, $productModel);
    $this->assertNotEquals('[]', $this->getActualOutput());
  }

  public function testRedirect()
  {
    $this->controller->redirect('testUrl');
    $this->assertEquals('testUrl', Yii::app()->user->getFlash('redirect'));
  }

  public function testActionIndex()
  {
    $this->controller->modelClass = 'BNews';
    $this->controller->actionIndex();

    $this->assertArrayHasKey('dataProvider', Yii::app()->user->getFlash('render'));
  }

  public function tearDown()
  {
    Yii::app()->db->createCommand()->truncateTable('{{product}}');
    Yii::app()->db->createCommand()->truncateTable('{{product_assignment}}');
  }
}

?>