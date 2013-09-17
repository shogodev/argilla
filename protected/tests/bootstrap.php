<?php
$config = require_once __DIR__.'/../config/test.php';

require_once(__DIR__.'/../../../yii/framework/yiit.php');
require_once(__DIR__.'/WebTestCase.php');
require_once(__DIR__.'/components/FTestApplication.php');

$app = new FTestApplication($config);