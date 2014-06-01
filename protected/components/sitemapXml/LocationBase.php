<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.sitemapXml.locationCenerators
 */
abstract class LocationBase extends CComponent implements ILocationGenerator
{
  /**
   * @var CDataProviderIterator
   */
  protected $_modelSource;
  /**
   * @var CController
   */
  protected $_controller;

  /**
   * @param CController $controller
   */
  function __construct(CController $controller)
  {
    $this->_controller = $controller;
  }

  /**
   * @return string
   */
  public abstract function getRoute();

  /**
   * @return string
   */
  public abstract function current();

  public function next()
  {
    $this->_modelSource->next();
  }

  /**
   * @return int
   */
  public function key()
  {
    return $this->_modelSource->key();
  }

  /**
   * @return bool
   */
  public function valid()
  {
    return $this->_modelSource->valid();
  }

  public function rewind()
  {
    $this->_modelSource->rewind();
  }

  public function unique(array $keys, array $dataSource)
  {
    foreach( $dataSource as $key => $data )
    {
      foreach( $dataSource as $key_ => $value_ )
      {
        if( $key != $key_ )
        {
          $delete = true;

          foreach( $keys as $value )
          {
            if( isset($dataSource[$key][$value]) && isset($dataSource[$key_][$value]) )
              if( $dataSource[$key][$value] != $dataSource[$key_][$value] )
                $delete = false;

            if( !isset($dataSource[$key][$value]) && isset($dataSource[$key_][$value]) )
              $delete = false;
          }

          if( $delete )
            unset($dataSource[$key_]);
        }
      }
    }

    return $dataSource;
  }
}