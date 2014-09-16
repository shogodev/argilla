<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.product
 *
 * @method static BFacetedParameter model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $parameter
 */
class BFacetedParameter extends BActiveRecord
{
  /**
   * @return array
   */
  public function getProperties()
  {
    return array_reduce($this->findAll(), function($result, $parameter) {
      if( $this->isPropertyValid($parameter->parameter) )
        $result[] = $parameter->parameter;
      return $result;
    }, []);
  }

  /**
   * @return array
   */
  public function getParameters()
  {
    return array_reduce($this->findAll(), function($result, $parameter) {
      if( is_numeric($parameter->parameter) )
        $result[] = $parameter->parameter;
      return $result;
    }, []);
  }

  /**
   * @param string $property
   *
   * @return bool
   */
  protected function isPropertyValid($property)
  {
    return in_array($property, CMap::mergeArray(
      array_keys(BProduct::model()->tableSchema->columns),
      array_keys(BProductAssignment::model()->tableSchema->columns)
    ));
  }
}