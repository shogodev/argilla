<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.commands
 */
Yii::import('frontend.share.helpers.Arr');
Yii::import('frontend.extensions.api-docs.*');

class ApiDocsCommand extends ApiCommand
{
  public $frontendOptions = array(
    'fileTypes' => array('php'),
    'exclude' => array(
      'views',
      'layouts',
      '/product',
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
    )
  );

  public $backendOptions = array(
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
    )
  );

  public function run($args)
  {
    if( Arr::get($args, '0') == 'build' )
    {
      Yii::setPathOfAlias('basePath', dirname(dirname(dirname(__FILE__))));
      $frontendDocsPath = Yii::getPathOfAlias('basePath.build.docs.frontend');

      $frontendApiModel = $this->getFrontendApiModel();
      echo "Building frontend api model...\n";

      $this->classes = $frontendApiModel->classes;
      $this->packages = $frontendApiModel->packages;

      echo "Building frontend pages...\n";
      $this->buildOfflinePages($frontendDocsPath, Yii::getPathOfAlias('frontend.extensions.api-docs'));
      $this->buildKeywords($frontendDocsPath);
      $this->buildPackages($frontendDocsPath);

      $backendDocsPath = Yii::getPathOfAlias('basePath.build.docs.backend');

      $backendApiModel = $this->getBackendApiModel();
      echo "Building backend api model...\n";

      $this->classes = $backendApiModel->classes;
      $this->packages = $backendApiModel->packages;

      echo "Building backend pages...\n";
      $this->buildOfflinePages($backendDocsPath, Yii::getPathOfAlias('frontend.extensions.api-docs'));
      $this->buildKeywords($backendDocsPath);
      $this->buildPackages($backendDocsPath);

      echo "Done.\n\n";
      exit();
    }

    if( Arr::get($args, '0') == 'check' )
    {
      $frontendFiles = CFileHelper::findFiles(Yii::getPathOfAlias('frontend'), $this->frontendOptions);
      $backendFiles = CFileHelper::findFiles(Yii::getPathOfAlias('backend'), $this->backendOptions);
      $model = new ApiModel;

      if( in_array(Arr::get($args, '1', 'param'), array('all', 'param')) )
      {
        $model->check($frontendFiles);
        $model->check($backendFiles);
      }

      if( in_array(Arr::get($args, '1', 'pack'), array('all', 'pack')) )
      {
        $model->checkPackages($frontendFiles);
        $model->checkPackages($backendFiles);
      }
      exit();
    }

    echo $this->getHelp();
  }

	public function getHelp()
  {
    return <<<EOD
USAGE
  createapidocs build
  createapidocs check [mode]

DESCRIPTION
  This command generates API documentation for the Argilla.

PARAMETERS
  * build: generate API documentation
  * check: check PHPDoc
  * mode: 'param', 'pack' or 'all' (default).
          'param' - check PHPDoc for proper @param syntax
          'pack'  - check PHPDoc for proper @package syntax
          'all' - check @param and @package
  [mode]


EXAMPLES
  * createapidocs build - builds api documentation in folder build/docs
  * createapidocs check - cheks PHPDoc @param directives

EOD;
  }

  protected function getFrontendApiModel()
  {
    $this->importDependencies(Yii::getPathOfAlias('frontend.config.frontend').'.php');
    return $this->buildModel(Yii::getPathOfAlias('frontend'), $this->frontendOptions);
  }

  protected function getBackendApiModel()
  {
    $this->importDependencies(Yii::getPathOfAlias('backend.config.backend').'.php');
    return $this->buildModel(Yii::getPathOfAlias('backend'),  $this->backendOptions);
  }

  protected function importDependencies($configPath)
  {
    $config = require_once $configPath;

    if( is_array($config) )
    {
      foreach($config['aliases'] as $name => $aliases)
        Yii::app()->setAliases(array($name => $aliases));

      foreach($config['import'] as  $file)
        Yii::import($file);
    }
  }

  protected function checkPackages($sourceFiles)
  {

  }
}