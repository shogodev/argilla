<?php
/**
 * @var GlobalConfig $globalConfig
 */
$globalConfig = GlobalConfig::instance();

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', true);

return array_merge_recursive(array(
  'basePath' => $globalConfig->frontendPath,
  'name' => 'argilla.ru',
  'defaultController' => 'index',
  'preload' => array('requestRedirect'),

  'aliases' => array(
    'backend' => $globalConfig->backendPath,
    'frontend' => $globalConfig->frontendPath,
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
    'frontend.models.product.showcase.*',
    'frontend.models.user.*',
    'frontend.models.info.*',
    'frontend.models.order.*',
    'frontend.models.order.components.*',
    'frontend.models.order.payment.*',
    'frontend.models.order.delivery.*',
    'frontend.models.order.paymentSystem.*',
    'frontend.models.order.paymentSystem.platron.*',
    'frontend.models.xml.*',
    'frontend.models.sitemap.*',
    'frontend.models.sitemap.generators.*',

    'frontend.components.*',
    'frontend.components.common.*',
    'frontend.components.validators.*',
    'frontend.components.redirect.*',
    'frontend.components.image.*',
    'frontend.components.form.*',
    'frontend.components.auth.*',
    'frontend.components.ar.*',
    'frontend.components.url.*',
    'frontend.components.collection.base.*',
    'frontend.components.collection.behaviors.*',
    'frontend.components.collection.*',
    'frontend.components.sitemap.*',
    'frontend.components.exceptions.*',

    'frontend.controllers.*',
    'frontend.controllers.behaviors.*',
    'frontend.widgets.*',
    'frontend.widgets.list_view.*',
    'frontend.widgets.basket.*',
    'frontend.forms.FormLayouts',
  ),

  'modules' => array(),

  'components' => array(
    'db' => array_replace_recursive(require($globalConfig->frontendConfigPath.'/db.php'),
      array(
        'enableProfiling' => YII_DEBUG,
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
      'useGulp' => !YII_DEBUG
    ),

    'user' => array(
      'class' => 'FWebUser',
      'allowAutoLogin' => true,
      'loginUrl' => '/user/login'
    ),

    'urlManager' => array(
      'class' => 'FUrlManager',
      'urlFormat' => 'path',
      'useStrictParsing' => true,
      'showScriptName' => false,
    ),

    'requestRedirect' => array(
      'class' => 'RequestRedirectComponent',
    ),

    'meta' => array(
      'class' => 'frontend.components.Meta',
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
          'enabled' => YII_DEBUG && isset($_COOKIE['YII_DEBUG']),
        ),
      ),
    ),

    'cache' => array(
      'class' => 'system.caching.CDummyCache',
    ),
  ),

  'params' => array(
    'mode' => 'frontend',
    'salt' => 'random_salt',
    'yandexSearchId' => '2193125'
  ),

  'sourceLanguage' => 'ru_ru',
  'language' => 'ru',
), require($globalConfig->frontendConfigPath.'/share.php'));