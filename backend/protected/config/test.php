<?php
$config = array_replace_recursive(
  require(dirname(__FILE__).'/backend.php'),
  array(
    'components' => array(
      'fixture' => array(
        'class' => 'BFixtureManager',
      ),
      'db' => array(
        'connectionString' => 'mysql:host=localhost;dbname=shogocms_tests',
        'tablePrefix'      => 'shogocms_',
      ),
    ),
    'import' => array(
      'backend.tests.components.*',
    ),
    'params' => array(
      'mode' => 'test'
    )
  )
);
unset($config['preload'][array_search('bootstrap', $config['preload'])]);
unset($config['components']['log']);

return $config;