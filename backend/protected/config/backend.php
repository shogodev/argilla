<?php
/**
 * @var GlobalConfig $globalConfig
 */
$globalConfig = GlobalConfig::instance();

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', true);

return array_merge_recursive(array(
  'name' => 'backend',
  'basePath' => $globalConfig->backendPath,
  'controllerPath' => $globalConfig->backendPath.'/controllers',
  'viewPath' => $globalConfig->backendPath.'/views',
  'runtimePath' => $globalConfig->backendPath.'/runtime',
  'defaultController' => 'base',

  'aliases' => array(
    'backend' => $globalConfig->backendPath,
    'frontend' => $globalConfig->frontendPath,
    'upload' => 'frontend.extensions.upload',
    'bootstrap' => 'frontend.extensions.bootstrap',
  ),

  'import' => array(
    'backend.models.*',
    'backend.models.behaviors.*',
    'backend.components.*',
    'backend.components.actions.*',
    'backend.components.auth.*',
    'backend.components.db.*',
    'backend.components.test.*',
    'backend.components.interfaces.*',
    'backend.controllers.*',
    'backend.widgets.*',
    'backend.widgets.grid.*',
    'backend.widgets.form.*',
    'backend.modules.rbac.components.*',
    'backend.modules.rbac.models.*',
    'backend.modules.menu.components.*',

    'ext.*',
    'ext.jtogglecolumn.*',
    'ext.wysiwyg.*',
    'ext.upload.components.*',
    'ext.upload.models.*',
    'ext.upload.grid.*',
    'ext.upload.*',
    'ext.onflyedit.*',
  ),

  'modules' => array(
    'gii' => array(
      'generatorPaths' => array('bootstrap.gii', 'backend.gii.generators'),
      'class' => 'system.gii.GiiModule',
      'password' => '123',
      'ipFilters' => array('192.168.*', '127.0.0.1', '::1'),
    ),
  ),

  'components' => array(

    'user' => array(
      'allowAutoLogin' => true,
      'loginUrl' => array('/'),
    ),

    'authManager' => array(
      'class' => 'CDbAuthManager',
      'connectionID' => 'db',
      'itemTable' => '{{auth_item}}',
      'itemChildTable' => '{{auth_item_child}}',
      'assignmentTable' => '{{auth_assignment}}',
      'defaultRoles' => array(
        'base',
        'help:help'
      )
    ),

    'clientScript' => array(
      'class' => 'ext.mainscript.components.SClientScript',
    ),

    'assetManager' => array(
      'class' => 'ext.mainscript.components.SAssetManager',
    ),

    'mainscript' => array(
      'class' => 'ext.mainscript.ScriptsFactory',
      'debug' => YII_DEBUG,
      'mode'  => 'backend',
    ),

    'urlManager' => array(
      'urlFormat' => 'path',
      'showScriptName' => false,
      'rules' => array(
        '<module:\w+>/<submodule:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<submodule>/<controller>/<action>',
        '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
        '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
        '<controller:\w+>/<id:\d+>' => '<controller>/view',
        '<module:\w+>/<submodule:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<submodule>/<controller>/<action>',
        '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
      ),
    ),

    'db' => array_replace_recursive(
      require($globalConfig->frontendConfigPath.'/db.php'),
      array(
        'enableProfiling'    => YII_DEBUG,
        'enableParamLogging' => YII_DEBUG,
      )
    ),

    'bootstrap' => array(
      'class' => 'ext.bootstrap.components.Bootstrap',
    ),

    'menu' => array(
      'class' => 'BMenu'
    ),

    'breadcrumbs' => array(
      'class' => 'BBreadcrumbsManager',
    ),

    'errorHandler' => array(
      'errorAction' => 'base/error',
    ),

    'log' => array(
      'class' => 'CLogRouter',
      'enabled' => true,
      'routes' => array(
        array(
          'class' => 'CFileLogRoute',
          'levels' => 'error, warning',
        ),
        array(
          'class' => 'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
          'ipFilters' => array('192.168.*'),
          'enabled'   => YII_DEBUG && isset($_COOKIE['YII_DEBUG']),
        ),
      ),
    ),
  ),

  'params' => array(
    'adminEmail' => 'glagolev@shogo.ru',
    // Уникальная соль, формируемая при разворачивании приложения
    'salt' => 'backup',
    // backend|console|test
    'mode' => 'backend',
  ),

  'sourceLanguage' => 'ru_ru',
  'language' => 'ru',
), require($globalConfig->frontendConfigPath.'/share.php'));
?>