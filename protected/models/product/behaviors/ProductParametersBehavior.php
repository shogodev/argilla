<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.protected.behaviors
 *
 * @property Product $id
 * @property ProductSection $section
 */
class ProductParametersBehavior extends CModelBehavior
{
  /**
   * @var ProductParameterName[]
   */
  protected $parameters;

  /**
   * @param null $key
   * @param CDbCriteria $groupCriteria критерия группы параметров
   * @param CDbCriteria|null|false $criteria критерия параметров
   *
   * @return ProductParameterName[]
   */
  public function getParameters($key = null, CDbCriteria $groupCriteria = null, $criteria = null)
  {
    if( !isset($this->parameters) )
    {
      $productParamNames = ProductParameterName::model();
      if( !is_null($groupCriteria) )
        $productParamNames->setGroupCriteria($groupCriteria);

      $productParamNames->addAssignmentCondition(array('section_id' => $this->owner->section->id));

      if( $criteria === null )
      {
        $criteria = new CDbCriteria();
        $criteria->compare('t.product', '1');
      }

      $this->parameters = $productParamNames->search($criteria);

      foreach($this->parameters as $parameter)
        $parameter->setProductId($this->owner->id);

      ProductParameter::model()->setParameterValues($this->parameters);
    }

    return isset($key) ? Arr::filter($this->parameters, array('groupKey', 'key'), $key) : $this->parameters;
  }

  /**
   * @param array $parameters
   *
   * @return $this
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
    return $this;
  }

  /**
   * @param $parameter
   */
  public function addParameter($parameter)
  {
    $this->parameters[] = $parameter;
  }

  /**
   * @return ProductParameterName[]
   */
  public function getProductOneParameters()
  {
    return $this->getParametersByAttributes(array('product' => 1), true);
  }

  /**
   * @return ProductParameterName[]
   */
  public function getProductLineParameters()
  {
    return $this->getParametersByAttributes(array('section_list' => 1), true);
  }

  /**
   * @return ProductParameterName[]
   */
  public function getProductTabletParameters()
  {
    return $this->getParametersByAttributes(array('section' => 1), true);
  }

  private function getParametersByAttributes(array $attributes, $notEmptyValue = true, $exceptionKeys = array())
  {
    $parameters = array();

    foreach($this->getParameters() as $parameter)
    {
      if( $notEmptyValue && empty($parameter->value) )
        continue;

      if( in_array($parameter->key, $exceptionKeys) )
        continue;

      if( !empty($attributes) )
      {
        foreach($attributes as $attribute => $value)
        {
          if( isset($parameter->{$attribute}) && $parameter->{$attribute} == $value )
            $parameters[] = $parameter;
        }
      }
      else
        $parameters[] = $parameter;
    }

    return $parameters;
  }
}