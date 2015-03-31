<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class AccessHelper
{
  /**
   * Массив со стандартными названием действий в контроллерах
   * и их человеко-понятным названием
   *
   * @var array
   */
  public static $baseActionNames = array(
    'index'  => 'Разводная',
    'update' => 'Обновление',
    'create' => 'Создание',
    'delete' => 'Удаление',
    'view'   => 'Просмотр',
  );

  /**
   * Массив с исключениями, по которым не проверяется доступ
   *
   * @var array
   */
  private static $excludes = array('help:help');

  private static $moduleList;

  private static $childList;

  private static $assignments;

  /**
   * @param BModule $module
   * @param BController $controller
   *
   * @return bool
   */
  public static function checkAccessByClasses(BModule $module = null, BController $controller)
  {
    if( get_class($controller) == 'BaseController' )
      return true;

    $task = self::getTaskName(get_class($module), get_class($controller));

    if( BRbacTask::taskExists($task) )
      return self::checkAccessByTask($task);

    if( !$module->enabled && !$controller->enabled )
      self::createTask($task, implode('-', array($module->name, $controller->name)));

    return false;
  }

  /**
   * @param string $moduleClass
   * @param string $controllerClass
   *
   * @return bool
   */
  public static function checkAccessByNameClasses($moduleClass, $controllerClass)
  {
    $task = self::getTaskName($moduleClass, $controllerClass);

    if( BRbacTask::taskExists($task) )
      return self::checkAccessByTask($task);

    $reflectionModule = new ReflectionClass($moduleClass);
    $reflectionModuleProperties = $reflectionModule->getDefaultProperties();

    if( !$reflectionModuleProperties['enabled'] )
      return false;

    $reflectionController = new ReflectionClass($controllerClass);
    $reflectionControllerProperties = $reflectionController->getDefaultProperties();

    if( !$reflectionControllerProperties['enabled'] )
      return false;

    self::createTask($task, implode('-', array($reflectionModuleProperties['name'], $reflectionControllerProperties['name'])));

    return false;
  }

  public static function filterModulesByAccess($modules)
  {
    $allowedModules = array();
    $assignments = self::getAssignments(Yii::app()->user->id);
    $childList = self::getChildList();

    foreach($assignments as $name => $assignment)
    {
      if( !isset($childList[$name]) )
        continue;

      foreach($modules as $module => $moduleData )
      {
        if( isset($allowedModules[$module]) )
          continue;

        $tasks = self::moduleList($module);
        if( array_intersect($tasks, Yii::app()->authManager->defaultRoles) || array_intersect($tasks, $childList[$name]) )
        {
          $allowedModules[$module] = $moduleData;
        }
      }
    }

    return $allowedModules;
  }

  public static function moduleList($module = null)
  {
    if( is_null(self::$moduleList) )
    {
      foreach(BRbacTask::getTasks() as $task => $title)
      {
        $delimiterPosition = strpos($task, ':');
        if( $delimiterPosition !== false )
          $moduleName = substr($task, 0, $delimiterPosition);
        else
          $moduleName = $task;

        self::$moduleList[$moduleName][$task] = $task;
      }
    }

    if( !$module )
      return self::$moduleList;

    if( isset(self::$moduleList[$module]) )
      return self::$moduleList[$module];

    return array();
  }

  public static function getChildList()
  {
    if( !is_null(self::$childList) )
      return self::$childList;

    $criteria = new CDbCriteria();
    $command = Yii::app()->db->commandBuilder->createFindCommand(Yii::app()->authManager->itemChildTable, $criteria);

    self::$childList = array();
    foreach($command->queryAll() as $item)
      self::$childList[$item['parent']][$item['child']] = $item['child'];

    return self::$childList;
  }

  public static function getAssignments($userId)
  {
    if( isset(self::$assignments[$userId]) )
      return self::$assignments[$userId];

    self::$assignments[$userId] = Yii::app()->authManager->getAuthAssignments($userId);

    return self::$assignments[$userId];
  }

  public static function clearCache()
  {
    self::$moduleList = null;
    self::$childList = null;
    self::$assignments = null;
  }

  private static function getTaskName($moduleClass, $controllerClass)
  {
    $moduleId = Utils::lcfirst(str_replace('Module', '', $moduleClass));
    $controllerId = Utils::lcfirst(str_replace('Controller', '', BApplication::cutClassPrefix($controllerClass)));

    return implode(':', array($moduleId, $controllerId));
  }

  private static function checkAccessByTask($task)
  {
    if( in_array($task, self::$excludes) )
      return true;

    return BRbacTask::checkTask($task, Yii::app()->user->id);
  }

  private static function createTask($taskName, $title)
  {
    $task = new BRbacTask();
    $task->title = $title;
    $task->name = $taskName;

    if( !$task->save() )
      throw new CHttpException(500, 'Не удалось создать задачу '.$taskName);
  }
}