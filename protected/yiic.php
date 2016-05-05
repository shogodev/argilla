<?php
/**
 * @var GlobalConfig $globalConfig
 */
require_once(__DIR__.'/config/global_config.php');
$config = require_once($globalConfig->frontendConfigPath.'/console.php');
require_once($globalConfig->frameworkPath.'/yiic.php');