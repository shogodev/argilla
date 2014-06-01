<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.sitemapXml
 */
class GeneratorFactory extends CComponent
{
  /**
   * @var ILocationGenerator[]
   */
  private $_generators;

  /**
   * @param string      $pathToGenerators
   * @param CController $controller
   */
  function __construct($pathToGenerators, CController $controller)
  {
    $generatorNames = new LocationGeneratorFileFilter(new DirectoryIterator($pathToGenerators));

    /** @var $generators ILocationGenerator[] */
    $generators = array();
    /** @var $name DirectoryIterator */
    foreach( $generatorNames as $name )
    {
      $className = $name->getBasename('.php');

      if($this->isSample($className))
        continue;

      $generator = new $className($controller);

      if( $generator instanceof ILocationGenerator )
      {
        $generators[] = $generator;
      }
    }

    $this->_generators = $generators;
  }

  /**
   * @return ILocationGenerator[]
   */
  public function getGenerators()
  {
    return $this->_generators;
  }

  /**
   * Checks if locationGenerator file is an example sample
   *
   * @param $className
   *
   * @return bool
   */
  public function isSample($className)
  {
    if(strpos($className, '.sample') !== false)
      return true;

    return false;
  }
}