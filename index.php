<?php
$production = __DIR__.'/protected/config/production.php';

if( file_exists($production) )
{
  error_reporting(0);
  ini_set('display_errors', 0);
  $config = require_once $production;
}
else
{
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  $config = require_once __DIR__.'/protected/config/frontend.php';
}

require_once(realpath(__DIR__.'/../yii/framework/yii.php'));
require_once('protected/components/FApplication.php');

$app = new FApplication($config);
$app->run();