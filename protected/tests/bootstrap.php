<?php
/**
 * @var GlobalConfig $globalConfig
 */
require_once realpath(__DIR__.'/../config/global_config.php');

require_once($globalConfig->frameworkPath.'/yiit.php');
require_once($globalConfig->frontendPath.'/tests/components/FTestApplication.php');

$app = new FTestApplication($globalConfig->frontendConfigPath.'/test.php');