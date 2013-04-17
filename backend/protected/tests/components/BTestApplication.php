<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.components
 */
require_once(dirname(__FILE__).'/../../components/BApplication.php');

class BTestApplication extends BApplication
{
  protected function init()
  {
    $_SERVER['SCRIPT_NAME']     = 'backend/index.php';
    $_SERVER['SCRIPT_FILENAME'] = dirname(__FILE__).'/../../../../'.$_SERVER['SCRIPT_NAME'];

    return parent::init();
  }

  /**
   * Устанавливаем окружение.
   * Задаем модуль, контроллер и экшен, в контексте которого выполняется код.
   *
   * Yii::app()->setUnitEnvironment('Info', 'BInfo', 'update', array('id' => '2'));
   *
   * @param string $module_name
   * @param string $controller_name
   * @param string $action
   * @param array  $params
   */
  public function setUnitEnvironment($module_name, $controller_name, $action = 'index', $params = array())
  {
    $module     = $module_name.'Module';
    $controller = $controller_name.'Controller';

    /**
     * @var BController $controller
     */
    $controller = new $controller(strtolower($controller_name), new $module(strtolower($module_name), null));
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
   */
  public function end($status = 0, $exit = true)
  {
    Yii::app()->user->setFlash('end', array('status' => $status, 'exit' => $exit));
    return;
  }
}