<?php
require_once(dirname(__FILE__).'/../../components/FApplication.php');

/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.components
 */
class TApplication extends FApplication
{
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
   * Завершаем приложение
   * Если приложение запущено с тестовым конфигом, то возвращаем управление в вызываемый код
   *
   * @param integer  $status
   * @param bool $exit
   */
  public function end($status = 0, $exit = true)
  {
    if( Yii::app()->params['mode'] === 'test' )
    {
      Yii::app()->user->setFlash('end', array('status' => $status, 'exit' => $exit));
      return;
    }
    else
    {
      parent::end($status, $exit);
    }
  }
}