<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components
 */
require_once(dirname(__FILE__).'/../../components/BApplication.php');

class BTestApplication extends BApplication
{
  protected function init()
  {
    $_SERVER['SCRIPT_FILENAME'] = realpath(__DIR__.'/../../../../'.$_SERVER['SCRIPT_NAME']);
    parent::init();
  }

  /**
   * Устанавливаем окружение.
   * Задаем модуль, контроллер и экшен, в контексте которого выполняется тест.
   *
   * Yii::app()->setUnitEnvironment('Info', 'BInfo', 'update', array('id' => '2'));
   *
   * @param string $moduleName
   * @param string $controllerName
   * @param string $action
   * @param array  $params
   */
  public function setUnitEnvironment($moduleName, $controllerName, $action = 'index', $params = array())
  {
    $moduleClass     = ucfirst($moduleName).'Module';
    $controllerClass = ucfirst($controllerName).'Controller';

    /**
     * @var BController $controller
     */
    $controller = new $controllerClass(strtolower($controllerName), new $moduleClass(strtolower($moduleName), null));
    $controller->setAction(new CInlineAction($controller, $action));

    Yii::app()->setController($controller);
    $_GET = CMap::mergeArray($_GET, $params);
  }

  public function setAjaxRequest()
  {
    $_SERVER['REQUEST_METHOD']        = 'POST';
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
  }

  /**
   * Инициализируем модули
   *
   * Функция используется в тестах, чтобы не инклудить модели
   */
  public function initModules()
  {
    $modules = Yii::app()->getModules();
    foreach($modules as $id => $module)
    {
      Yii::import($module['class']);

      $className = ucfirst($id).'Module';
      $class     = new $className($id, null);
    }
  }

  /**
   * @param integer $status
   * @param bool $exit
   *
   * @throws BTestEndException
   */
  public function end($status = 0, $exit = true)
  {
    throw new BTestEndException(200, 'Application is shut down', $status);
  }
}