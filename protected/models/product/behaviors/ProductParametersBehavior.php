<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models.product.behaviors
 */

/**
 * Class ProductParametersBehavior
 * Поведение для работы с параметрами продукта
 *
 * @property Product $owner
 */
class ProductParametersBehavior extends CModelBehavior
{
  /**
   * @var ProductParameterName[]
   */
  protected $parameters;

  protected $basketParameter;

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

      if( empty($this->owner->parent) )
        $productParamNames->addAssignmentCondition(array('section_id' => $this->owner->section->id));

      if( $criteria === null )
      {
        $criteria = new CDbCriteria();
        $criteria->compare('t.product', '1');
        $criteria->compare('t.key', ProductParameter::BASKET_KEY, false, 'OR');
      }
      $criteria->addInCondition('t.id', $this->getCurrentProductParameterNameIds());

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
  public function setParameters($parameters = array())
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

  /**
   * @return ProductParameterName|null
   */
  public function getBasketParameter()
  {
    if( is_null($this->basketParameter) )
    {
      $this->basketParameter = $this->getParameterByKey(ProductParameter::BASKET_KEY);
    }
    return $this->basketParameter;
  }

  /**
   * @param string|array $key
   * @param bool $notEmptyOnly
   *
   * @return null|ProductParameterName
   */
  public function getParameterByKey($key, $notEmptyOnly = true)
  {
    $parameters = array();

    foreach($this->getParameters() as $parameter)
    {
      if( (is_array($key) && in_array($parameter->key, $key)) || ($parameter->key == $key) )
      {
        if( $notEmptyOnly && empty($parameter->value) )
          continue;

        $parameters[] = $parameter;
      }
    }

    return is_array($key) ? $parameters : Arr::reset($parameters);
  }

  /**
   * @param $id
   * @param bool $noEmpty
   *
   * @return null|ProductParameterName
   */
  public function getParameterById($id, $noEmpty = true)
  {
    foreach($this->getParameters() as $parameter)
    {
      if( $parameter->id == $id )
      {
        if( $noEmpty && empty($parameter->value) )
          break;

        return $parameter;
      }
    }

    return null;
  }

  private function getCurrentProductParameterNameIds()
  {
    $criteria = new CDbCriteria();
    $criteria->compare('product_id', $this->owner->primaryKey);
    $criteria->select = 'param_id';

    $command = Yii::app()->db->commandBuilder->createFindCommand(ProductParameter::model()->tableName(), $criteria);

    return $command->queryColumn();
  }

  /**
   * @param string $name
   * @param string $value
   * @param string $key
   *
   * @return ProductParameter|stdClass
   */
  private function createFakeParameter($name, $value, $key = 'fake')
  {
    $parameter = new stdClass();
    $parameter->name = $name;
    $parameter->value = $value;
    $parameter->key = $key;
    $parameter->id = null;

    return $parameter;
  }

  private function getParametersByAttributes(array $attributes, $notEmptyValue = true, $exceptionKeys = array())
  {
    $parameters = array();

    foreach($this->getParameters() as $parameter)
    {
      if( $notEmptyValue && empty($parameter->value) )
        continue;

      if( in_array($parameter->key, $exceptionKeys) || in_array($parameter->getGroupKey(), $exceptionKeys)  )
        continue;

      if( !empty($attributes) )
      {
        $attributesSuccess = false;
        foreach($attributes as $attribute => $value)
        {
          if( isset($parameter->{$attribute}) && $parameter->{$attribute} == $value )
            $attributesSuccess = true;
          else
          {
            $attributesSuccess = false;
            break;
          }
        }
        if( $attributesSuccess )
          $parameters[] = $parameter;
      }
      else
        $parameters[] = $parameter;
    }

    return $parameters;
  }
}
