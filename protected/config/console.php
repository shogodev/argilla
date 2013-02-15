<?php
$frontend  = dirname(dirname(__FILE__));
$backend   = dirname(dirname(dirname(__FILE__))) . '/backend/protected';

Yii::setPathOfAlias('frontend', $frontend);
Yii::setPathOfAlias('backend', $backend);

$config = array(
  'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
  'name' => 'Console Application',
  'preload' => array('log'),

  'components' => array(
    'db' => CMap::mergeArray(require($frontend.'/config/db.php'),
      array()
    ),
    'log' => array(
      'class' => 'CLogRouter',
      'routes' => array(
        array(
          'class' => 'CFileLogRoute',
          'levels' => 'error, warning',
        ),
      ),
    ),
  ),
  'params' => array(
    'mode' => 'console'
  )
);

$config['commandMap'] = array(
  'migrate' => array(
    'class' => 'system.cli.commands.MigrateCommand',
    'migrationTable' => $config['components']['db']['tablePrefix'].'migration',
  ),
);

return $config;