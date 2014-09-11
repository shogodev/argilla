<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.commands
 */

Yii::import('backend.components.*');
Yii::import('backend.components.db.*');
Yii::import('frontend.share.*');
Yii::import('frontend.extensions.upload.*');
Yii::import('frontend.extensions.upload.components.*');

class ModuleCleanerCommand extends CConsoleCommand
{
  /**
   * Получение списка модулей
   */
  public function actionList()
  {
    $path = Yii::getPathOfAlias('backend.modules');

    $directoryIterator = new DirectoryIterator($path);

    echo '-----------------------------------'.PHP_EOL;
    echo 'Modules:'.PHP_EOL;
    echo '-----------------------------------'.PHP_EOL;

    foreach( $directoryIterator as $fileInfo )
    {
      /**
       * @var DirectoryIterator $fileInfo
       */
      if( $fileInfo->isDot() ) continue;

      echo 'Name: '.$fileInfo->getFilename().'; Path:';
      echo $fileInfo->getRealPath().PHP_EOL;
      echo '-----------------------------------'.PHP_EOL;
    }
  }

  /**
   * Полное удаление модуля
   *  Удаление всех таблиц моделей модуля
   *  Удаление директории
   *
   * @param string $module
   *
   * @return int
   */
  public function actionDelete($module)
  {
    if( !$this->moduleExists($module) )
    {
      echo 'Модуль '.$module.' не доступен '.PHP_EOL;
      return 0;
    }
    elseif( $this->confirm('Вы действительно хотите удалить модуль '.$module) )
    {
      Yii::import('backend.modules.'.$module.'.*');

      $moduleName = ucfirst($module).'Module';

      if( class_exists($moduleName) === false )
      {
        echo 'Класс модуля '.$moduleName.' не существует'.PHP_EOL;
        return 0;
      }

      /**
       * @var BModule $moduleClass
       */
      $moduleClass = new $moduleName($module, null);
      foreach( $moduleClass->moduleDependencies as $dependency )
      {
        if( $this->moduleExists($dependency) )
        {
          echo 'Невозможно удалить модуль, так как он зависит от '.$dependency.PHP_EOL;
          return 0;
        }
      }

      $this->clearDb($module);
      $this->deleteModuleDirectory($module);
    }
    else
      return 0;
  }

  /**
   * Проверка на существование модуля
   *
   * @param string $module
   *
   * @return bool
   */
  protected function moduleExists($module)
  {
    $path = Yii::getPathOfAlias('backend.modules');
    $modulePath = $path.DIRECTORY_SEPARATOR.$module;

    return file_exists($modulePath) && is_dir($modulePath);
  }

  protected function clearDb($module)
  {
    Yii::import('backend.modules.'.$module.'.models.*');
    $path = Yii::getPathOfAlias('backend.modules.'.$module.'.models');

    $db = Yii::app()->db;
    $db->createCommand('SET foreign_key_checks = 0')->execute();

    foreach( CFileHelper::findFiles($path) as $modelFile )
    {
      $classPathParts = explode('/', $modelFile);
      $classFile = end($classPathParts);
      $classFileParts = explode('.', $classFile);
      $className = $classFileParts[0];

      echo 'Найдена модель '.$className.PHP_EOL;

      /**
       * @var BActiveRecord $model
       */
      $model = new $className();
      $table = $model->tableName();

      if( $this->confirm('Вы действительно хотите удалить таблицу '.$table.'?') )
        $db->createCommand()->dropTable($table);
    }

    $db->createCommand('SET foreign_key_checks = 1')->execute();
  }

  /**
   * @param string $module
   *
   * @return bool
   */
  protected function deleteModuleDirectory($module)
  {
    $basePath = Yii::getPathOfAlias('backend.modules.'.$module);
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath),
                                              RecursiveIteratorIterator::CHILD_FIRST);
    $pathInfo = new SplFileInfo($basePath);

    echo '-----------------------------------'.PHP_EOL;

    if( !$pathInfo->isWritable() )
    {
      echo 'Невозможно удалить модуль '.$module.'. Нет прав на запись'.PHP_EOL;
      return false;
    }

    foreach( $iterator as $path )
    {
      /**
       * @var SplFileInfo $path
       */
      $pathName = $path->__toString();

      if( $path->isDir() )
      {
        if( $path->getFilename() === '.' || $path->getFilename() === '..' )
          continue;

        rmdir($pathName);
      }
      else
        unlink($pathName);

      echo 'Удалено: '.$pathName.PHP_EOL;
    }

    rmdir($basePath);

    echo '-----------------------------------'.PHP_EOL;

    return true;
  }
}