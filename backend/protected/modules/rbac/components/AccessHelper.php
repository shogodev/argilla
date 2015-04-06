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
   */
  public static $baseActionNames = array(
    'index'  => 'Разводная',
    'update' => 'Обновление',
    'create' => 'Создание',
    'delete' => 'Удаление',
    'view'   => 'Просмотр',
  );

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
    if( is_null($module) )
      return self::checkAccessByController($controller);

    $task = self::getTaskName(get_class($module), get_class($controller));

    if( BRbacTask::taskExists($task) )
      return self::checkAccessByTask($task);
    else if( $module->enabled && $controller->enabled )
      self::createTask($task, implode(' - ', array($module->name, $controller->name)));

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
    else
    {
      $reflectionModule = new ReflectionClass($moduleClass);
      $reflectionModuleProperties = $reflectionModule->getDefaultProperties();

      if( !$reflectionModuleProperties['enabled'] )
        return false;

      $reflectionController = new ReflectionClass($controllerClass);
      $reflectionControllerProperties = $reflectionController->getDefaultProperties();

      if( !$reflectionControllerProperties['enabled'] )
        return false;

      self::createTask($task, implode(' - ', array($reflectionModuleProperties['name'], $reflectionControllerProperties['name'])));
    }

    return false;
  }

  /**
   * @param array $modules
   *
   * @return array
   */
  public static function filterModulesByAccess($modules)
  {
    $allowedModules = array();

    foreach($modules as $module => $moduleData )
    {
      $tasks = self::getModuleList($module);

      foreach($tasks as $task)
      {
        if( self::checkAccessByTask($task) )
        {
          $allowedModules[$module] = $moduleData;
          continue 2;
        }
      }
    }

    return $allowedModules;
  }

  public static function clearCache()
  {
    self::$moduleList = null;
    self::$childList = null;
    self::$assignments = null;
  }

  private static function checkAccessByController(BController $controller)
  {
    $task = self::getTaskName(null, get_class($controller));

    if( BRbacTask::taskExists($task) )
      return self::checkAccessByTask($task);
    else if( $controller->enabled )
      self::createTask($task, implode(' - ', array($controller->name)));

    return false;
  }

  private static function getModuleList($module = null)
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

  private static function getChildList()
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

  private static function getAssignments($userId)
  {
    if( isset(self::$assignments[$userId]) )
      return self::$assignments[$userId];

    self::$assignments[$userId] = Yii::app()->authManager->getAuthAssignments($userId);

    return self::$assignments[$userId];
  }

  private static function getTaskName($moduleClass = null, $controllerClass)
  {
    $task = array();

    if( isset($moduleClass) )
      $task['moduleId'] = Utils::lcfirst(str_replace('Module', '', $moduleClass));

    $task['controllerId'] = Utils::lcfirst(str_replace('Controller', '', BApplication::cutClassPrefix($controllerClass)));

    return implode(':', $task);
  }

  private static function checkAccessByTask($task)
  {
    if( in_array($task, Yii::app()->authManager->defaultRoles) )
      return true;

    $userId = Yii::app()->user->id;

    $assignments = self::getAssignments($userId);
    $childList = self::getChildList();

    foreach($assignments as $name => $assignment)
    {
      if( !isset($childList[$name]) )
        continue;

      if( isset($childList[$name][$task]) )
        return true;
    }

    return false;
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