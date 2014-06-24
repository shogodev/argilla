<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.commands
 */
Yii::import('frontend.commands.api.*');

class CheckPhpDocCommand extends CConsoleCommand
{
  public function run($args)
  {
    $files = array();

    if( !isset($args[0]) || in_array($args[0], array('frontend', 'backend', 'all')) )
    {
      if( !isset($args[0]) || $args[0] != 'backend' )
        $files = CMap::mergeArray($files, $this->getFrontendFiles());

      if( !isset($args[0]) || $args[0] != 'frontend' )
        $files = CMap::mergeArray($files, $this->getBackendFiles());
    }
    else
    {
      echo $this->help;
      exit();
    }

    if( !isset($args[1]) || in_array($args[1], array('property', 'package', 'param', 'all')) )
    {
      $model = new checkPhpDoc();

      if( !isset($args[1]) || in_array($args[1], array('param', 'all')) )
        $model->checkParams($files);

      if( !isset($args[1]) || in_array($args[1], array('package', 'all')) )
        $model->checkPackage($files);

      if( !isset($args[1]) || in_array($args[1], array('property', 'all')) )
        $model->checkProperties($files);
    }
    else
    {
      echo $this->help;
      exit();
    }
  }

  public function getHelp()
  {
    return <<<EOD
USAGE
  checkphpdoc [frontend|backend|all] [property|package|param|all]

DESCRIPTION
  This command check valid phpDoc.

EXAMPLES
  * checkphpdoc - check all
  * checkphpdoc frontend property - check @property in frontend

EOD;
  }

  protected function getFrontendFiles()
  {
    $path = str_replace(DIRECTORY_SEPARATOR.'commands', '', dirname(__FILE__));

    $config = require_once Yii::getPathOfAlias('frontend.config.frontend').'.php';

    if( is_array($config) )
    {
      foreach($config['aliases'] as $name => $aliases)
        Yii::app()->setAliases(array($name => $aliases));

      foreach($config['import'] as  $file)
        Yii::import($file);
    }

    $files = CFileHelper::findFiles($path, array(
      'fileTypes' => array('php'),
      'exclude' => array(
        'views',
        'layouts',
        '/config',
        '/extensions',
        '/forms',
        '/migrations',
        '/tests',
        '/runtime',
        'yiilite.php',
        'yiit.php',
        'yii.php',
        'yiic.php',
        'DBRule.php'
      )
    ));

    return $files;
  }

  protected function getBackendFiles()
  {
    $path = str_replace('protected'.DIRECTORY_SEPARATOR.'commands', '', dirname(__FILE__));

    Yii::app()->setAliases(array('frontend' =>$path.'protected'.DIRECTORY_SEPARATOR));

    $path .= 'backend'.DIRECTORY_SEPARATOR.'protected';

    $config = require_once Yii::getPathOfAlias('backend.config.backend').'.php';

    if( is_array($config) )
    {
      foreach($config['aliases'] as $name => $aliases)
        Yii::app()->setAliases(array($name => $aliases));

      foreach($config['import'] as  $file)
        Yii::import($file);
    }

    $files = CFileHelper::findFiles($path, array(
      'fileTypes' => array('php'),
      'exclude' => array(
        'views',
        'layouts',
        '/config',
        '/extensions',
        '/forms',
        '/migrations',
        '/tests',
        '/runtime',
        '/gii',
        'ProductImageGrid.php'
      )
    ));

    return $files;
  }
}