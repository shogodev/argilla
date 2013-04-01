<?php
$config = require_once dirname(__FILE__).'/../config/test.php';
$yiit   = dirname(__FILE__).'/../../../yii/framework/yiit.php';

require_once($yiit);
require_once(dirname(__FILE__).'/WebTestCase.php');
require_once(dirname(__FILE__).'/components/TApplication.php');

Yii::setPathOfAlias('backend', $backend);
Yii::setPathOfAlias('frontend', $frontend);

$app = new TApplication($config);