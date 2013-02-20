<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.rbac
 *
 * @example
 * В контроллере, доступ на который необходимо проверить
 * <code>
 *  $access = new AccessHelper();
 *  $access->checkAccess();
 * </code>
 *
 * Будет происходить проверка по шаблону module:controller
 *
 * Для проверки на доступ к текущему экшену:
 * @example
 * <code>
 *  $access = new AccessHelper();
 *  $access->checkAccess(true);
 * </code>
 *
 *
 * Для того, чтобы проверить удаленный контроллер
 * @example
 * <code>
 *  $access = new AccessHelper($module, $controlle, $action)
 * </code>
 *
 * Так же можно получить объект класса через статический метод init()
 *
 * @example
 * <code>
 *  AccessHelper::init($module, $controller, $action)->checkAccess();
 * </code>
 */
class AccessHelper
{

  /**
   * Массив со стандартными названием действий в контроллерах
   * и их человеко-понятным названием
   *
   * @var array
   */
  public $baseActionNames = array(
    'index'  => 'Главная',
    'update' => 'Обновление',
    'create' => 'Создание',
    'delete' => 'Удаление',
    'view'   => 'Просмотр',
  );

  /**
   * Модуль
   *
   * @var type
   */
  private $module;

  /**
   * Контроллер
   *
   * @var string
   */
  private $controller;

  /**
   * Экшен для контроллера
   *
   * @var string
   */
  private $action;

  /**
   * объект менеджера авторизации
   *
   * @var CAuthManager
   */
  private $auth;

  /**
   * Название задачи
   *
   * module:controller
   *
   * @var string
   */
  private $taskName = '';

  /**
   * Название операции
   *
   * module:controller:action
   *
   * @var string
   */
  private $operationName = '';

  /**
   * Массив с исплючениями, по которым не проверяется доступ
   *
   * @var array
   */
  private $excludes = array('base:error', 'base', 'help:help');

  /**
   * Задача входа пользователя в систему
   *
   * @var string
   */
  private $loginOperation = 'base:login';

  /**
   * @param null $module
   * @param null $controller
   * @param null $action
   */
  public function __construct($module = null, $controller = null, $action = null)
  {
    $this->initProperties($module, $controller, $action);
    $this->createTaskName();
    $this->createOperationName();
  }

  /**
   * @return taskName
   */
  public function getTaskName()
  {
    return $this->taskName;
  }

  /**
   * @return operationName
   */
  public function getOperationName()
  {
    return $this->operationName;
  }

  /**
   * @param string|null $module
   * @param string|null $controller
   * @param string|null $action
   *
   * @return AccessHelper
   */
  public static function init($module = null, $controller = null, $action = null)
  {
    return new AccessHelper($module, $controller, $action);
  }

  /**
   * Проверка на доступ к модулю
   *
   * @param string $moduleName
   *
   * @return boolean
   */
  public static function checkAssessToModule($moduleName)
  {
    if( self::isServerDev() && Yii::app()->user->id === 1 )
      return true;

    $result = false;
    $tasks = BRbacTask::model()->findAll('name LIKE "' . $moduleName . '%"');

    foreach( $tasks as $task )
    {
      if( $result === true )
        break;

      $result = Yii::app()->user->checkAccess($task->name, array('userId' => Yii::app()->user->id));
    }

    return $result;
  }


  /**
   * Проверка на доступ к текущему указанному контроллеру
   * Для проверки доступа по action необходимо установить флаг $useOperations в TRUE
   *
   * @param boolean $useOperation
   *
   * @return boolean
   */
  public function checkAccess($useOperation = false)
  {
    if( $this->loginOperation === $this->operationName )
      return true;

    if( self::isServerDev() && !Yii::app()->user->isGuest )
      return true;

    if( !$useOperation )
      return $this->checkTaskAccess();
    else
    {
      $this->checkTaskAccess(); // create task
      return $this->checkOperationAccess();
    }
  }

  /**
   * Проверка на расположение сервера
   *
   * @return boolean
   */
  public static function isServerDev()
  {
    return file_exists(BUserIdentity::ALLOW_FREE_AUTH);
  }

  /**
   * Проверка доступа по задаче (контроллеру)
   *
   * @return boolean
   */
  private function checkTaskAccess()
  {
    if( in_array($this->taskName, $this->excludes) )
      return true;

    if( BRbacTask::taskExists($this->taskName) )
      return Yii::app()->user->checkAccess($this->taskName, array('userId' => Yii::app()->user->id));
    else
    {
      $this->createTask();
      $this->fillAccessData();
      return false;
    }
  }

  /**
   * Проверка доступа по операции (контроллер->экшен)
   *
   * @return boolean
   */
  protected function checkOperationAccess()
  {
    if( in_array($this->operationName, $this->excludes) )
      return true;

    if( BRbacOperation::operationExists($this->operationName) )
      return Yii::app()->user->checkAccess($this->operationName, array('userId' => Yii::app()->user->id));
    else
    {
      $this->createOperation();
      $this->fillAccessData();

      return false;
    }
  }

  /**
   * Создание системного имени задачи
   */
  protected function createTaskName()
  {
    if( !empty($this->module) )
      $this->taskName = $this->module . ':';

    $this->taskName .= $this->controller;
  }

  /**
   * Создание системного имени операции
   */
  protected function createOperationName()
  {
    $this->operationName = $this->taskName . ':' . $this->action;
  }

  /**
   * Создание человеко-понятного имени задачи
   *
   * @return string
   */
  protected function createTaskHumanityName()
  {
    $humanityName = $this->taskName;

    if( !empty($this->module) )
    {
      if( (boolean)$controller = $this->loadController() )
      {
        $moduleName = ucfirst($this->module) . 'Module';
        $module = new $moduleName($this->module, 'Module');
        $humanityName = $module->name . '-' . $controller->name;
      }

    }

    return $humanityName;
  }

  /**
   * Создание человеко-понятного имени операции
   *
   * @return string
   */
  protected function createOperationHumanityName()
  {
    $humanityName = $this->operationName;

    if( !empty($this->module) )
    {
      if( (boolean)$controller = $this->loadController() )
      {
        if( method_exists($controller, 'action' . ucfirst($this->action)) )
          $humanityName = $this->createTaskHumanityName() . '-' . $this->baseActionNames[$this->action];
      }
    }

    return $humanityName;
  }

  /**
   * Инициализация параметров для проверки доступа
   *
   * @param string $module
   * @param string $controller
   * @param string $action
   */
  private function initProperties($module, $controller, $action)
  {
    if( $module === null || $controller === null )
    {
      if( !empty(Yii::app()->controller->module->id) )
      {
        $module           = Yii::app()->controller->module;
        $this->module     = $module->id;
        $this->controller = array_search(get_class(Yii::app()->controller), $module->controllerMap);
      }
      else
        $this->controller = Yii::app()->controller->id;

      $this->action = Yii::app()->controller->action->id;
    }
    else
    {
      $this->module     = $module;
      $this->controller = $controller;
      $this->action     = $action;
    }

    $this->auth = Yii::app()->authManager;
  }


  /**
   * Создание новой записи задачи
   */
  private function createTask()
  {
    $task = new BRbacTask();
    $task->title = $this->createTaskHumanityName();
    $task->name = $this->taskName;
    $task->save(false);
  }

  /**
   * Создание новой записи операции
   */
  private function createOperation()
  {
    $operation = new BRbacOperation;
    $operation->title = $this->createOperationHumanityName();
    $operation->name  = $this->operationName;
    $operation->save(false);
  }

  /**
   * Загружает контроллер, если контроллер не найден, возращает false
   *
   * @return CController|boolean
   */
  private function loadController()
  {
    try
    {
      Yii::import('backend.modules.' . $this->module . '.*');
      Yii::import('backend.modules.' . $this->module . '.controllers.*');

      $controllerName = $this->controller . 'Controller';

      if( @class_exists($controllerName) !== false )
      {
        $controller = new $controllerName($this->controller);
        return $controller;
      }
    }
    catch( Exception $e )
    {
      Yii::log($e->getMessage());
    }

    return false;
  }

  /**
   * Добавление новых операции в существующую задачу
   */
  private function fillAccessData()
  {
    if( !empty($this->operationName) )
    {
      $parts  = explode(':', $this->operationName);
      $parent = $parts[0] . ':' . $parts[1];

      if( $parent == $this->taskName )
      {
        if( !BRbacTask::taskExists($this->taskName) )
          $this->createTask();

        if( !BRbacOperation::operationExists($this->operationName) )
          $this->createOperation();

        $this->auth->addItemChild($this->taskName, $this->operationName);
      }

    }
  }
}