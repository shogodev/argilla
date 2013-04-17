<?php
defined('YII_ENABLE_EXCEPTION_HANDLER') or define('YII_ENABLE_EXCEPTION_HANDLER', false);
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', false);

$config = array_replace_recursive(
  require(dirname(__FILE__).'/backend.php'),
  array(
    'components' => array(
      'fixture' => array(
        'class' => 'SFixtureManager',
      ),
    ),
    'aliases' => array(
      'bootstrap' => 'ext.bootstrap'
    ),
    'import' => array(
      'backend.tests.components.*',
    ),
    'params' => array(
      'mode' => 'test'
    )
  )
);

unset($config['components']['log']);
$config['components']['db']['connectionString'] = preg_replace("/dbname=([\w\-_]+)/", "dbname=$1_test", $config['components']['db']['connectionString']);

return $config;