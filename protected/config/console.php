<?php
/**
 * @var GlobalConfig $globalConfig
 */
$globalConfig = GlobalConfig::instance();

$config = array(
  'basePath' => $globalConfig->frontendPath,
  'name' => 'Console Application',
  'preload' => array('log'),

  'aliases' => array(
    'backend' => $globalConfig->backendPath,
    'frontend' => $globalConfig->frontendPath,
  ),

  'import' => array(
    'frontend.components.cli.*',
    'frontend.commands.components.*'
  ),

  'components' => array(
      'db' => require($globalConfig->frontendConfigPath.'/db.php'
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
      'class' => 'SFormatter',
      'datetimeFormat' => 'd.m.Y H:i:s',
      'dateFormat' => 'd.m.Y',
      'timeFormat' => 'H:i:s',
      'numberFormat' => array(
        'decimals' => 0,
        'decimalSeparator' => ',',
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