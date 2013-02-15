<?php
$config = array_replace_recursive(
  require(dirname(__FILE__).'/frontend.php'),
  array(
    'components' => array(
      'fixture' => array(
        'class' => 'backend.components.BFixtureManager',
        'basePath' => Yii::getPathOfAlias('frontend.tests.fixtures'),
      ),
      'db' => array(
        'connectionString' => 'mysql:host=localhost;dbname=shogocms_tests',
        'tablePrefix' => 'shogocms_',
      ),
    ),

    'params' => array(
      'mode' => 'test'
    )
  )
);

return $config;