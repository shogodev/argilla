<?php
$production = dirname(__FILE__).'/protected/config/production.php';

if( file_exists($production) )
{
  error_reporting(0);
  $config = require_once $production;
}
else
{
  error_reporting(E_ALL);
  $config = require_once dirname(__FILE__).'/protected/config/frontend.php';
}

$yii = dirname(__FILE__).'/../yii/framework/yii.php';
require_once($yii);
require_once('protected/components/FApplication.php');

Yii::setPathOfAlias('backend', $backend);
Yii::setPathOfAlias('frontend', $frontend);

$app = new FApplication($config);
$app->run();