<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', true);

$backend  = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'backend/protected';
$frontend = dirname(dirname(__FILE__));

return array(
  'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
  'name' => 'argilla.ru',
  'defaultController' => 'index',
  'preload'  => array('log', 'seoRedirect', 'bootstrap'),

  'aliases' => array(
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
    'frontend.models.user.*',

    'frontend.components.*',
    'frontend.components.interfaces.*',
    'frontend.components.validators.*',
    'frontend.components.alphabets.*',
    'frontend.components.redirect.*',
    'frontend.components.image.*',
    'frontend.components.form.*',
    'frontend.components.auth.*',
    'frontend.components.ar.*',

    'frontend.controllers.*',
    'frontend.controllers.behaviors.*',
    'frontend.widgets.*',

    'share.*',
  ),

  'modules' => array(),

  'components' => array(

    'db' => array_replace_recursive(require(dirname(__FILE__).'/db.php'),
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
    ),

    'user' => array(
      'class'          => 'FWebUser',
      'allowAutoLogin' => true,
      'loginUrl'       => '/'
    ),

    'urlManager' => array(
      'class'            => 'FUrlManager',
      'urlFormat'        => 'path',
      'useStrictParsing' => true,
      'showScriptName'   => false,
      'rules'            => require(dirname(__FILE__).'/routes.php'),
    ),

    'errorHandler' => array(
      'errorAction' => 'error/error',
    ),

    'format' => array(
      'class' => 'frontend.components.formatters.FFormatter',
      'datetimeFormat' => 'd.m.Y H:i:s',
      'dateFormat'     => 'd.m.Y',
      'timeFormat'     => 'H:i:s',
      'numberFormat'   => array(
        'decimals'          => 0,
        'decimalSeparator'  => ',',
        'thousandSeparator' => ' ')
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
    ),

    'bootstrap' => array(
      'class' => 'ext.bootstrap.components.Bootstrap',
      'responsiveCss' => true,
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
    'salt'        => 'random_salt',
    'collectUrls' => false,
    'cacheUrls'   => false,
  ),

  'sourceLanguage' => 'ru_ru',
  'language' => 'ru',
);