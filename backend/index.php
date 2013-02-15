<?php
$config = require_once 'protected/config/backend.php';
$yii    = dirname(__FILE__).'/../../yii/framework/yii.php';

require_once($yii);
require_once('protected/components/BApplication.php');

Yii::setPathOfAlias('backend', $backend);
Yii::setPathOfAlias('frontend', $frontend);

$app = new BApplication($config);
$app->run();