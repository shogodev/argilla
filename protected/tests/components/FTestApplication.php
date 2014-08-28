<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.components
 */
require_once(dirname(__FILE__).'/../../components/FApplication.php');


/**
 * Class FTestApplication
 * @property THttpRequest $request The request override component.
 */
class FTestApplication extends FApplication
{
  protected function init()
  {
    $_SERVER['SCRIPT_FILENAME'] = realpath(__DIR__.'/../../..'.$_SERVER['SCRIPT_NAME']);
    parent::init();
    Yii::setPathOfAlias('webroot', realpath(Yii::getPathOfAlias('frontend').'/..'));
  }

  /**
   * Устанавливаем окружение.
   * Задаем контроллер и экшен, в контексте которого выполняется тест.
   *
   * Yii::app()->setUnitEnvironment('Info', 'update', array('id' => '2'));
   *
   * @param string $controllerName
   * @param string $action
   * @param array  $params
   */
  public function setUnitEnvironment($controllerName, $action = 'index', $params = array())
  {
    $class = ucfirst($controllerName).'Controller';

    /**
     * @var FController $controller
     */
    $controller = new $class(strtolower($controllerName));
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