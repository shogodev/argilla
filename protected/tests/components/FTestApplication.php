<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.components
 */
require_once(dirname(__FILE__).'/../../components/FApplication.php');

class FTestApplication extends FApplication
{
  protected function init()
  {
    $_SERVER['SCRIPT_NAME']     = '/index.php';
    $_SERVER['SCRIPT_FILENAME'] = realpath(__DIR__.'/../../../'.$_SERVER['SCRIPT_NAME']);
    $_SERVER['REQUEST_URI']     = realpath(__DIR__.'/../../../'.$_SERVER['SCRIPT_NAME']);

    parent::init();
  }

  /**
   * Устанавливаем окружение.
   * Задаем контроллер и экшен, в контексте которого выполняется код.
   *
   * Yii::app()->setUnitEnvironment('Info', 'update', array('id' => '2'));
   *
   * @param string $controller_name
   * @param string $action
   * @param array  $params
   */
  public function setUnitEnvironment($controller_name, $action = 'index', $params = array())
  {
    $controller = $controller_name.'Controller';

    $controller = new $controller(strtolower($controller_name));
    $controller->setAction(new CInlineAction($controller, $action));

    Yii::app()->setController($controller);
    $_GET = CMap::mergeArray($_GET, $params);
  }

  /**
   * @param integer $status
   * @param bool $exit
   *
   * @throws TEndException
   */
  public function end($status = 0, $exit = true)
  {
    throw new TEndException(200, 'Application is shut down', $status);
  }
}