<?php
/**
 * @var GlobalConfig $globalConfig
 */
require_once __DIR__.'/config/global_config.php';

$yiic = $globalConfig->frameworkPath.'/yiic.php';
$config = $globalConfig->frontendConfigPath.'/console.php';

require_once($yiic);
