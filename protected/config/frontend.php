<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', true);

$frontend = realpath(__DIR__.'/..');
$backend = realpath(__DIR__.'/../../backend/protected');

return array_replace_recursive(array(
  'basePath' => $frontend,
  'name' => 'argilla.ru',
  'defaultController' => 'index',
  'preload'  => array('log', 'seoRedirect'),

  'aliases' => array(
    'backend' => $backend,
    'frontend' => $frontend,
    'nestedset' => 'ext.nested-set-behavior',
    'share' => 'frontend.share',
  ),

  'import' => array(
    'frontend.models.*',

    'frontend.models.seo.*',
    'frontend.models.news.*',
    'frontend.models.contact.*',
    'frontend.models.menu.*',
    'frontend.models.product.*',
    'frontend.models.product.filter.*',
    'frontend.models.user.*',
    'frontend.models.info.*',
    'frontend.models.order.*',
    'frontend.models.xml.*',

    'frontend.components.*',
    'frontend.components.session.*',
    'frontend.components.common.*',
    'frontend.components.interfaces.*',
    'frontend.components.validators.*',
    'frontend.components.alphabets.*',
    'frontend.components.redirect.*',
    'frontend.components.image.*',
    'frontend.components.form.*',
    'frontend.components.auth.*',
    'frontend.components.ar.*',
    'frontend.components.url.*',
    'frontend.components.collection.*',

    'frontend.controllers.*',
    'frontend.controllers.behaviors.*',
    'frontend.widgets.*',

    'share.*',
    'share.validators.*',
    'share.formatters.*',
  ),

  'modules' => array(),

  'components' => array(

    'db' => array_replace_recursive(require(__DIR__.'/db.php'),
      array(
        'enableProfiling'    => YII_DEBUG,
        'enableParamLogging' => YII_DEBUG,
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
    ),

    'compare' => array(
      'class' => 'frontend.models.ProductCompare',
    ),

    'urlCollection' => array(
      'class' => 'frontend.components.UrlCollection',
      'collectUrls' => false,
    ),

    'user' => array(
      'class'          => 'FWebUser',
      'allowAutoLogin' => true,
      'loginUrl'       => '/user/login'
    ),

    'session' => array(
      'class' => 'FHttpSession',
      'sessionName' => 'FRONTEND',
    ),

    'urlManager' => array(
      'class'            => 'FUrlManager',
      'urlFormat'        => 'path',
      'useStrictParsing' => true,
      'showScriptName'   => false,
      'rules'            => require(__DIR__.'/routes.php'),
    ),

    'errorHandler' => array(
      'errorAction' => 'error/error',
    ),

    'log' => array(
      'class' => 'CLogRouter',
      'enabled' => true,
      'routes'  => array(
        array(
          'class'  => 'CFileLogRoute',
          'levels' => 'error, warning',
        ),
        array(
          'class'     => 'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
          'ipFilters' => array('192.168.*'),
          'enabled'   => false,
        ),
      ),
    ),

    'email' => array(
      'class'    => 'ext.email.Email',
      'delivery' => 'php',
    ),

    'notification' => array(
      'class' => 'share.SNotification',
    ),

    'seoRedirect' => array(
      'class' => 'RedirectHelper',
      'cacheUrls' => false,
    ),

    'phpThumb' => array(
      'class' => 'ext.phpthumb.EPhpThumb',
      'options' => array()
    ),

    'cache' => array(
      'class'=>'system.caching.CDummyCache',
    ),
  ),

  'params' => array(
    'mode'        => 'frontend',
    'salt'        => 'random_salt',
  ),

  'sourceLanguage' => 'ru_ru',
  'language' => 'ru',
), require(__DIR__.'/share.php'));