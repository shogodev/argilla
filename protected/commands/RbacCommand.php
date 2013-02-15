<?php

Yii::import('backend.components.*');
Yii::import('backend.components.auth.*');
Yii::import('backend.components.db.*');
Yii::import('backend.controllers.*');

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.commands
 */
class RbacCommand extends CConsoleCommand
{
  /**
   * username администратора
   *
   * @var string
   */
  protected $username;

  /**
   * Пароль для администратора
   *
   * @var string
   */
  protected $password;

  /**
   * Путь к корню приложения
   *
   * @var string
   */
  protected $path;

  /**
   * Путь к директории модулей, относительно корня приложения
   *
   * @var string
   */
  protected $modulePath;

  /**
   * Массив названий модулей
   *
   * @var array
   */
  protected $modules = array();

  /**
   * Массив массивов, содержащих, название и имена задач
   *
   * @var array
   */
  protected $names = array();

  /**
   * @var CDbAuthManager
   */
  protected $authManager;

  /**
   * Администратор
   *
   * @var User
   */
  protected $user;

  /**
   * Системное имя для роли администратора
   *
   * @var string
   */
  protected $roleSystemName = 'root';

  /**
   * @param        $path
   * @param string $modulePath
   * @param string $username
   * @param string $password
   */
  public function actionBuild($path = null, $modulePath = '/backend/protected/modules', $username = 'root', $password = '123')
  {
    if( $path === null )
      $path = dirname(dirname(dirname(__FILE__)));

    $this->path       = $path;
    $this->modulePath = $modulePath;
    $this->username   = $username;
    $this->password   = $password;

    echo "-------------------------------------------------------------\n";
    $this->getFiles();
    echo "-------------------------------------------------------------\n";
    $this->getNames();

    $this->createUser();
    echo "-------------------------------------------------------------\n";
    $this->createRecords();
    echo "-------------------------------------------------------------\n";
  }

  /**
   * Удаление все информации RBAC
   */
  public function actionClear()
  {
    try
    {
      Yii::import('backend.components.*');
      Yii::import('backend.modules.rbac.models.*');

      BUser::model()->deleteAll();
      BRbacTask::model()->deleteAll();

      $db = new CDbCommand(Yii::app()->db);
      $db->truncateTable($this->getAuthManager()->itemChildTable);
      $db->truncateTable($this->getAuthManager()->assignmentTable);

      echo "Выполнено\n";
    }
    catch( Exception $e )
    {
      echo $e->getMessage() . "\n";
    }
  }

  /**
   * Получение всех модулей и контроллеров
   */
  protected function getFiles()
  {
    if( file_exists($this->path . $this->modulePath) )
    {
      $handle = opendir($this->path . $this->modulePath);

      while( false !== ($entry = readdir($handle)) )
      {
        if( $entry !== '.' && $entry !== '..' )
          $this->loadModule($entry);
      }
    }
  }

  /**
   * Загрузка контроллеров модуля
   *
   * @param string $module
   */
  protected function loadModule($module)
  {
    if( !file_exists($this->path . $this->modulePath . '/' . $module . '/controllers') )
      return;

    echo "Найден модуль: $module\n";

    $handle = opendir($this->path . $this->modulePath . '/' . $module . '/controllers');

    while( false !== ($entry = readdir($handle)) )
    {
      if( $entry !== '.' && $entry !== '..' )
      {
        $this->modules[$module]['controllers'][] = $entry;
        Yii::import('backend.modules.' . $module . '.controllers.*');
      }

    }
  }

  /**
   * Создание названий и имел задач
   */
  protected function getNames()
  {
    foreach( $this->modules as $module => $item )
    {
      foreach( $item['controllers'] as $controller )
      {
        Yii::import('backend.modules.' . $module . '.*');

        $controllerName = explode('.', $controller);
        $controller = new $controllerName[0]($controllerName[0]);

        $moduleClass = ucfirst($module) . 'Module';
        $moduleModel = new $moduleClass($module, $module);

        if( $controller instanceof SecureController )
        {
          $this->names[] = array(
            'title' => $moduleModel->name . ' - ' . $controller->name,
            'name' => $module . ':' . str_replace('Controller', '', $controller->id),
          );
        }
      }
    }
  }

  /**
   * Создание задач RBAC
   */
  protected function createRecords()
  {
    Yii::import('backend.modules.rbac.models.*');

    $this->assignRoot();

    foreach( $this->names as $name )
    {
      $task        = new BRbacTask();
      $task->name  = $name['name'];
      $task->title = $name['title'];
      $task->type  = CAuthItem::TYPE_TASK;

      try
      {
        $task->save();
        $this->getAuthManager()->addItemChild($this->username, $task->id);
        echo "$task->title ($task->name) успешно создано\n";
      }
      catch( Exception $e )
      {
        echo "$task->name уже создано\n";
      }
    }
  }

  /**
   * @return CDbAuthManager
   */
  protected function getAuthManager()
  {
    if( empty($this->authManager) )
      $this->initAuthManager();

    return $this->authManager;
  }

  /**
   * Инициализация CDbAuthManager
   */
  protected function initAuthManager()
  {
    $this->authManager                  = new CDbAuthManager();
    $this->authManager->itemTable       = '{{auth_item}}';
    $this->authManager->itemChildTable  = '{{auth_item_child}}';
    $this->authManager->assignmentTable = '{{auth_assignment}}';
    $this->authManager->init();
  }

  /**
   * Создание учетной записи администратора
   *
   * @return void
   */
  protected function createUser()
  {
    Yii::import('backend.modules.rbac.models.*');

    if( BUser::model()->exists('username = :username', array(':username' => $this->username)) )
      $this->user = BUser::model()->find('username = :username', array(':username' => $this->username));
    else
    {
      $this->user = new BUser();
      $this->user->username = $this->username;
      $this->user->password = $this->password;
      $this->user->save();
    }
  }

  /**
   * Создание прав для администратора
   *
   * @return void
   */
  protected function assignRoot()
  {
    $role = BRbacRole::model()->find('title = :title AND type = :type AND name = :sysname', array(
      ':title' => 'Администратор', ':type' => CAuthItem::TYPE_ROLE, ':sysname' => $this->roleSystemName)
    );

    if( empty($role) )
    {
      $root = new BRbacRole();
      $root->name = $this->roleSystemName;
      $root->title = 'Администратор';
      $root->type  = CAuthItem::TYPE_ROLE;
      $root->save();
    }
    else
      $root = clone $role;

    try
    {
      $this->getAuthManager()->assign($root->name, $this->user->id);
    }
    catch( Exception $e )
    {
      // ????
    }

  }
}