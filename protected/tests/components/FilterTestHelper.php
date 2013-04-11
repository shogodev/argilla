<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.filter
 */
class FilterTestHelper
{
  protected $parameters;

  public function createStateByName($data)
  {
    $state = array();

    foreach($data as $elementName => $value)
    {
      $element = $this->findElementIdByName($elementName, !is_array($value) ? array($value => $value) : $value);

      if( empty($element) )
        throw new ErrorException('elementName does not find');

      if( is_array($value) )
        $state = Arr::mergeAssoc($state, $element);
      else
        $state[key($element)] = reset($element[key($element)]);
    }

    return $state;
  }

  private function findElementIdByName($elementName, $value)
  {
    $result = null;

    if( in_array($elementName, array('section_id', 'type_id')) )
    {
      $model = $elementName == 'section_id' ? ProductSection::model() : ProductType::model();

      $result = $this->findInModel($model, $elementName, $value);
    }

    if( !$result )
      $result = $this->findInParameter($elementName, $value);

    return $result;
  }

  private function findInModel($model, $elementName, $values)
  {
    $data = array();

    foreach($values as $value)
    {
      $item = $model->findByAttributes(array('name' => $value));

      if( !$item )
        throw new ErrorException('elementName does not find in '.get_class($model));

      $data[$elementName][$item->id] = $item->id;
    }

    return $data;
  }

  private function findInParameter($elementName, $values)
  {
    $data = array();

    $parameters = $this->getFilterParameters();

    foreach($parameters as $parameter)
    {
      if( $parameter->name == $elementName )
        $paramId = $parameter->id;
    }

    if( !isset($paramId) )
      return null;

    foreach($values as $value)
    {
      $item = ProductParamVariant::model()->findByAttributes(array('param_id' => $paramId, 'name' => $value));

      if( !$item )
        throw new ErrorException('elementName does not find in ProductParamVariant');

      $data[$paramId][$item->id] = $item->id;
    }

    return $data;
  }

  private function getFilterParameters()
  {
    if( empty($this->parameters) )
    {
      $criteria = new CDbCriteria();
      $criteria->compare('`key`', 'filter');

      $this->parameters = ProductParam::model()->getParameters($criteria);
    }

    return $this->parameters;
  }
}