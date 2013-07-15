<?php

Yii::import('backend.components.*');
Yii::import('backend.components.interfaces.*');
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
  protected $modulePath = '/backend/protected/modules';

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
   * @var CAuthItem
   */
  protected $root;

  /**
   * Системное имя для роли администратора
   *
   * @var string
   */
  protected $roleSystemName = 'admin';

  /**
   * @param string $username
   * @param string $password
   */
  public function actionBuild($username = 'admin', $password = '123')
  {
    $this->path       = dirname(dirname(dirname(__FILE__)));
    $this->username   = $username;
    $this->password   = $password;

    echo "-------------------------------------------------------------".PHP_EOL;
    $this->getFiles();
    echo "-------------------------------------------------------------".PHP_EOL;
    $this->getNames();

    $this->createUser();
    echo "-------------------------------------------------------------".PHP_EOL;
    $this->createRecords();
    echo "-------------------------------------------------------------".PHP_EOL;
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

      echo "Выполнено".PHP_EOL;
    }
    catch( Exception $e )
    {
      echo $e->getMessage().PHP_EOL;
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

    Yii::import('backend.modules.'.$module.'.*');
    $moduleName = ucfirst($module).'Module';

    if( @class_exists($moduleName) !== false )
    {
      echo "Найден модуль: $module\n";

      $moduleClass = new $moduleName($module, null);
      $this->modules[$module] = $moduleClass;
    }
  }

  /**
   * Создание названий и имен задач
   */
  protected function getNames()
  {
    foreach( $this->modules as $moduleName => $module )
    {
      foreach( $module->controllerMap as $id => $controller )
      {
        $controllerClass = new $controller($id);

        $this->names[] = [
          'name' => $moduleName.':'.$controllerClass->getId(),
          'title' => $module->name.' - '.$controllerClass->name,
        ];
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
        $this->getAuthManager()->addItemChild($this->root->name, $task->id);
        echo "$task->title ($task->name) успешно создано".PHP_EOL;
      }
      catch( Exception $e )
      {
        echo "$task->name уже создано".PHP_EOL;
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
   */
  protected function createUser()
  {
    Yii::import('backend.modules.rbac.models.*');

    $criteria = new CDbCriteria();
    $criteria->compare('username', $this->username);
    $this->user = BUser::model()->find($criteria);

    if( $this->user === null )
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
    $title = 'Администратор';

    $criteria = new CDbCriteria();
    $criteria->compare('title', $title);
    $criteria->compare('type',CAuthItem::TYPE_ROLE);
    $criteria->compare('name', $this->roleSystemName);

    $this->root = BRbacRole::model()->find($criteria);

    if( empty($this->root) )
    {
      $this->root = new BRbacRole();
      $this->root->name = $this->roleSystemName;
      $this->root->title = $title;
      $this->root->type  = CAuthItem::TYPE_ROLE;
      $this->root->save();
    }
    else
      $this->root = clone $this->root;

    try
    {
      if( !$this->getAuthManager()->isAssigned($this->root->name, $this->user->id) )
        $this->getAuthManager()->assign($this->root->name, $this->user->id);
    }
    catch( Exception $e )
    {
      // ????
    }

  }
}