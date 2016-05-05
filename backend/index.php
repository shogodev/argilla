<?php
/**
 * @var GlobalConfig $globalConfig
 */
require_once(__DIR__.'/../protected/config/global_config.php');
$config = require_once($globalConfig->backendConfigPath.'/backend.php');
require_once($globalConfig->frameworkPath.'/yii.php');
require_once($globalConfig->backendPath.'/components/BApplication.php');

$app = new BApplication($config);
$app->run();