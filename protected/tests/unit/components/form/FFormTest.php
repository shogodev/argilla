<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class FFormTest extends CTestCase
{
  public function setUp()
  {
    Yii::app()->setUnitEnvironment('index', 'index');
  }

  public function testDuplicateValidateMessages()
  {
    $this->setPost(array('TestForm' => array('name' => '')));

    $form = new FForm(array('elements' => array('name' => array('type' => 'text'))), new TestForm());
    $form->ajaxSubmit = false;
    $form->validateOnChange = false;
    $form->validateOnSubmit = false;

    $form->process();
    $form->render();

    $this->assertEquals(1, count($form->model->errors));
  }

  private function setPost(array $data)
  {
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = $data;
  }
} 