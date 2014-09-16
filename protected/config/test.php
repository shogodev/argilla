<?php
defined('YII_ENABLE_EXCEPTION_HANDLER') or define('YII_ENABLE_EXCEPTION_HANDLER', false);
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', false);

$config = array_replace_recursive(
  require(__DIR__.'/frontend.php'),
  array(
    'import' => array(
      'testComponents' => 'frontend.tests.components.*',
      'testForms' => 'frontend.tests.components.form.*',
    ),
    'components' => array(
      'fixture' => array(
        'class' => 'share.SFixtureManager',
        'basePath' => 'frontend.tests.fixtures',
      ),
      'request' => array(
        'class' => 'frontend.tests.components.THttpRequest'
      ),
      'user' => array(
        'class' => 'TWebUser',
        'allowAutoLogin' => true,
        'loginUrl' => '/user/login'
      ),
    ),
    'params' => array(
      'mode' => 'test'
    )
  )
);

unset($config['components']['log']);
$config['components']['db']['connectionString'] = preg_replace("/dbname=([\w\-_]+)/", "dbname=$1_test", $config['components']['db']['connectionString']);

return $config;