<?php
$config = require_once 'protected/config/backend.php';

require_once(realpath(__DIR__.'/../../yii/framework/yii.php'));
require_once('protected/components/BApplication.php');

$app = new BApplication($config);
$app->run();