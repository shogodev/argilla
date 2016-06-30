<?php
/**
 * @var GlobalConfig $globalConfig
 */
require_once(__DIR__.'/../config/global_config.php');
$config = require_once($globalConfig->frontendConfigPath.'/test.php');
require_once($globalConfig->frameworkPath.'/yiit.php');
require_once($globalConfig->frontendPath.'/tests/components/FTestApplication.php');

$app = new FTestApplication($config);