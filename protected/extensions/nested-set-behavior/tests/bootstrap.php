<?php
/**
 * @var GlobalConfig $globalConfig
 */
require_once(__DIR__.'/../../../config/global_config.php');
$config = require_once(dirname(__FILE__).'/config/test.php');
require_once($globalConfig->frameworkPath.'/yiit.php');

Yii::createWebApplication($config);