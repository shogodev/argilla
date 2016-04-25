<?php
/**
 * @var GlobalConfig $globalConfig
 */
require_once __DIR__.'/../../../config/global_config.php';

$yiit = $globalConfig->frameworkPath.'/yiit.php';
$config = dirname(__FILE__).'/config/test.php';

require_once($yiit);

Yii::createWebApplication($config);