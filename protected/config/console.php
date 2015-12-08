<?php
mb_internal_encoding("UTF-8");
mb_http_output("UTF-8");

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
    'frontend.components.cli.*',
    'frontend.commands.components.*'
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
    'format' => array(
      'class'          => 'SFormatter',
      'datetimeFormat' => 'd.m.Y H:i:s',
      'dateFormat'     => 'd.m.Y',
      'timeFormat'     => 'H:i:s',
      'numberFormat'   => array(
        'decimals'          => 0,
        'decimalSeparator'  => ',',
        'thousandSeparator' => ' '
      )
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