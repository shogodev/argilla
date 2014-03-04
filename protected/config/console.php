<?php
$frontend = realpath(__DIR__.'/..');
$backend = realpath(__DIR__.'/../../backend/protected');

$config = array(
  'basePath' => $frontend,
  'name' => 'Console Application',
  'preload' => array('log'),

  'aliases' => array(
    'backend' => $backend,
    'frontend' => $frontend,
  ),

  'import' => array(
    'frontend.extensions.api-docs.*'
  ),

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