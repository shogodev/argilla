<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>, Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.commands
 */

Yii::import('backend.components.*');
Yii::import('backend.components.interfaces.*');
Yii::import('backend.components.auth.*');
Yii::import('backend.components.db.*');
Yii::import('backend.controllers.*');

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
    $this->username = $username;
    $this->password = $password;

    echo "-------------------------------------------------------------".PHP_EOL;
    $this->findModules(Yii::getPathOfAlias('backend'));
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
      $db->delete($this->getAuthManager()->itemChildTable);
      $db->delete($this->getAuthManager()->assignmentTable);

      echo "Выполнено".PHP_EOL;
    }
    catch( Exception $e )
    {
      echo $e->getMessage().PHP_EOL;
    }
  }

  protected function findModules($basePath)
  {
    $modulesPath = $basePath.DIRECTORY_SEPARATOR.'modules';

    foreach(glob($modulesPath.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR) as $moduleDirectory)
    {
      if( preg_match("/\w+/", basename($moduleDirectory)) )
      {
        $this->loadModule($moduleDirectory);
        $this->findModules($moduleDirectory);
      }
    }
  }

  protected function loadModule($moduleDirectory)
  {
    $moduleName = basename($moduleDirectory);

    Yii::setPathOfAlias($moduleName, $moduleDirectory);
    $moduleClass = ucfirst($moduleName).'Module';
    Yii::import($moduleName.'.'.$moduleClass);

    if( class_exists($moduleClass) !== false )
    {
      if( preg_match_all('/modules\/(\w+)/', $moduleDirectory, $matches) )
        $moduleLabel = implode(':', $matches[1]);
      else
        $moduleLabel = $moduleName;

      /**
       * @var BModule $module
       */
      $module = new $moduleClass($moduleName, null);

      if( !$module->enabled )
        echo "Модуль $moduleLabel отключен\n";

      if( empty($module->controllerMap) )
        return;

      echo "Найден модуль: $moduleLabel\n";

      $this->modules[$moduleName] = $module;
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
        $class = new ReflectionClass($controller);
        $properties = $class->getDefaultProperties();

        $this->names[] = [
          'name' => $moduleName.':'.$id,
          'title' => $module->name.' - '.$properties['name'],
          'enabled' => $properties['enabled']
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
      $task = new BRbacTask();
      $task->name  = $name['name'];
      $task->title = $name['title'];
      $task->type = CAuthItem::TYPE_TASK;

      if( !$name['enabled'] )
      {
        echo "$task->name отключено ".PHP_EOL;
        continue;
      }

      try
      {
        $task->save();
        $this->getAuthManager()->addItemChild($this->root->name, $task->name);
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
    $this->authManager = new CDbAuthManager();
    $this->authManager->itemTable = '{{auth_item}}';
    $this->authManager->itemChildTable = '{{auth_item_child}}';
    $this->authManager->assignmentTable = '{{auth_assignment}}';
    $this->authManager->init();
  }

  /**
   * Создание учетной записи администратора
   */
  protected function createUser()
  {
    Yii::import('backend.modules.rbac.models.*');
    BUser::model()->deleteAllByAttributes(array('username' => $this->username, 'password' => ''));

    $criteria = new CDbCriteria();
    $criteria->compare('username', $this->username);
    $this->user = BUser::model()->find($criteria);

    if( $this->user === null )
    {
      $this->user = new BUser();
      $this->user->username = $this->username;
      $this->user->setNewPassword($this->password);
      $this->user->save();
      echo "Создан пользователь ".$this->username." с паролем ".$this->password.PHP_EOL;
    }
    else
      echo "Пользователь ".$this->username." уже существует".PHP_EOL;
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
    $criteria->compare('type', CAuthItem::TYPE_ROLE);
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
    }
  }
}