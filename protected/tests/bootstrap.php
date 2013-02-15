<?php
$yiit   = dirname(__FILE__).'/../../../yii/framework/yiit.php';
$config = dirname(__FILE__).'/../config/test.php';

require_once($yiit);
require_once(dirname(__FILE__).'/WebTestCase.php');

$backend  = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'backend/protected';
$frontend = dirname(dirname(__FILE__));

Yii::setPathOfAlias('backend', $backend);
Yii::setPathOfAlias('frontend', $frontend);

Yii::createWebApplication($config);
?>