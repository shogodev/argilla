<?php
/**
 * @var GlobalConfig $globalConfig
 */
require_once(__DIR__.'/../../../protected/config/global_config.php');
$config = require_once($globalConfig->backendConfigPath.'/test.php');
require_once($globalConfig->frameworkPath.'/yiit.php');
require_once($globalConfig->backendPath.'/tests/components/BTestApplication.php');

$app = new BTestApplication($config);
$app->initModules();