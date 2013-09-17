<?php
defined('YII_ENABLE_EXCEPTION_HANDLER') or define('YII_ENABLE_EXCEPTION_HANDLER', false);
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', false);

$config = array_replace_recursive(
  require(__DIR__.'/frontend.php'),
  array(
    'import' => array(
      'tests' => 'frontend.tests.components.*',
    ),
    'components' => array(
      'fixture' => array(
        'class' => 'share.SFixtureManager',
        'basePath' => 'frontend.tests.fixtures',
      ),
      'request' => array(
        'class' => 'frontend.tests.components.THttpRequest'
      ),
      'session' => array(
        'class' => 'CHttpSession'
      )
    ),
    'params' => array(
      'mode' => 'test'
    )
  )
);

unset($config['components']['log']);
$config['components']['db']['connectionString'] = preg_replace("/dbname=([\w\-_]+)/", "dbname=$1_test", $config['components']['db']['connectionString']);

return $config;