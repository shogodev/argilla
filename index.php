<?php
/**
 * @var GlobalConfig $globalConfig
 */
require_once __DIR__.'/protected/config/global_config.php';

$production = $globalConfig->frontendConfigPath.'/production.php';
if( file_exists($production) )
{
  error_reporting(0);
  ini_set('display_errors', 0);
  $yii = 'yiilite.php';
  $config = require_once $production;
}
else
{
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  $yii = 'yii.php';
  $config = require_once $globalConfig->frontendConfigPath.'/frontend.php';
}

require_once($globalConfig->frameworkPath.'/'.$yii);
require_once($globalConfig->frontendPath.'/components/FApplication.php');

$app = new FApplication($config);
$app->run();