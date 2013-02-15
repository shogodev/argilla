<?php
$config = require_once dirname(__FILE__).'/../config/test.php';
$yiit   = dirname(__FILE__).'/../../../../yii/framework/yiit.php';

require_once($yiit);
require_once(dirname(__FILE__).'/WebTestCase.php');
require_once(dirname(__FILE__).'/../components/BApplication.php');

Yii::setPathOfAlias('backend', $backend);
Yii::setPathOfAlias('frontend', $frontend);

$_SERVER['SCRIPT_NAME']     = 'backend/index.php';
$_SERVER['SCRIPT_FILENAME'] = '/usr/www/shogocms/html/backend/index.php';
$_SERVER['SERVER_NAME']     = 'shogocms.dev.shogo.ru';
$_SERVER['HTTP_HOST']       = 'shogocms.dev.shogo.ru';
$_SERVER['REQUEST_URI']     = '/backend/';

$app = new BApplication($config);
$app->initModules();
?>