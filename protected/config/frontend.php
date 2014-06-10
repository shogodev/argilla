<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', true);

$frontend = realpath(__DIR__.'/..');
$backend = realpath(__DIR__.'/../../backend/protected');

return array_merge_recursive(array(
  'basePath' => $frontend,
  'name' => 'argilla.ru',
  'defaultController' => 'index',
  'preload'  => array('requestRedirect'),

  'aliases' => array(
    'backend' => $backend,
    'frontend' => $frontend,
  ),

  'import' => array(
    'frontend.models.*',
    'frontend.models.behaviors.*',

    'frontend.models.seo.*',
    'frontend.models.news.*',
    'frontend.models.contact.*',
    'frontend.models.menu.*',
    'frontend.models.product.*',
    'frontend.models.product.behaviors.*',
    'frontend.models.product.filter.*',
    'frontend.models.user.*',
    'frontend.models.info.*',
    'frontend.models.order.*',
    'frontend.models.order.paymentSystem.*',
    'frontend.models.order.paymentSystem.platron.*',
    'frontend.models.xml.*',
    'frontend.models.sitemap.*',
    'frontend.models.sitemap.generators.*',

    'frontend.components.*',
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
    'frontend.components.sitemap.*',


    'frontend.controllers.*',
    'frontend.controllers.behaviors.*',
    'frontend.widgets.*',
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

    'user' => array(
      'class'          => 'FWebUser',
      'allowAutoLogin' => true,
      'loginUrl'       => '/user/login'
    ),

    'urlManager' => array(
      'class'            => 'FUrlManager',
      'urlFormat'        => 'path',
      'useStrictParsing' => true,
      'showScriptName'   => false,
      'rules'            => require(__DIR__.'/routes.php'),
    ),

    'requestRedirect' => array(
      'class' => 'RequestRedirectComponent',
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
          'enabled'   => YII_DEBUG,
        ),
      ),
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