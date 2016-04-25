<?php
/**
 * @var GlobalConfig $globalConfig
 */
require_once __DIR__.'/../../../protected/config/global_config.php';

require_once($globalConfig->frameworkPath.'/yiit.php');
require_once($globalConfig->backendPath.'/tests/components/BTestApplication.php');

$app = new BTestApplication($globalConfig->backendConfigPath.'/test.php');
$app->initModules();