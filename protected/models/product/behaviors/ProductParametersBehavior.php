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

  /**
   * @var array
   */
  private $cache;

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
   * @param $id
   * @param bool $notEmptyOnly
   *
   * @return null|ProductParameterName
   */
  public function getParameterById($id, $notEmptyOnly = true)
  {
    return $this->getParametersByAttributes(array('id' => $id), true, $notEmptyOnly);
  }

  /**
   * @param array $idList
   * @param bool|true $notEmptyOnly
   *
   * @return null|ProductParameterName[]
   */
  public function getParametersByIdList(array $idList, $notEmptyOnly = true)
  {
    return $this->getParametersByAttributes(array('id' => $idList), false, $notEmptyOnly);
  }

  /**
   * @param string|array $key
   * @param bool $notEmptyOnly
   *
   * @return null|ProductParameterName
   */
  public function getParameterByKey($key, $notEmptyOnly = true )
  {
    return $this->getParametersByAttributes(array('key' => $key), true, $notEmptyOnly);
  }

  /**
   * @param string|array $keys
   * @param bool $notEmptyOnly
   *
   * @return null|ProductParameterName[]
   */
  public function getParametersByKeys(array $keys, $notEmptyOnly = true)
  {
    return $this->getParametersByAttributes(array('key' => $keys), false, $notEmptyOnly);
  }

  /**
   * @return ProductParameterName[]
   */
  public function getParametersCard()
  {
    return $this->getParametersByAttributes(array('product' => 1));
  }

  /**
   * @return ProductParameterName[]
   */
  public function getParametersLine()
  {
    return $this->getParametersByAttributes(array('section_list' => 1));
  }

  /**
   * @return ProductParameterName[]
   */
  public function getParametersTablet()
  {
    return $this->getParametersByAttributes(array('section' => 1));
  }

  /**
   * @return ProductParameterName|null
   */
  public function getParametersBasket()
  {
    return $this->getParametersByKeys(array(ProductParameter::BASKET_KEY));
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

  /**
   * Выбирает параметры по заданным атрибутам
   * Пример:
   * $this->getParametersByAttributes(array('section_list' => 1));
   * $this->getParametersByAttributes(array('key' => array('test', 'test2')));
   * $this->getParametersByAttributes(array('id' => 'test'), true);
   *
   * @param array $attributes
   * @param bool $onlyOne только один параметр
   * @param bool|true $notEmptyValue
   * @param array $exceptionKeys
   *
   * @return array
   * @throws CHttpException
   */
  private function getParametersByAttributes(array $attributes, $onlyOne = false, $notEmptyValue = true, $exceptionKeys = array())
  {
    $cacheKey = serialize(func_get_args());

    if( !isset($this->cache[$cacheKey]) )
    {
      $parameters = array();

      foreach($this->getParameters() as $parameter)
      {
        if( $notEmptyValue && empty($parameter->value) )
          continue;

        if( !empty($exceptionKeys) && (in_array($parameter->key, $exceptionKeys) || in_array($parameter->getGroupKey(), $exceptionKeys)) )
          continue;

        if( !empty($attributes) )
        {
          $attributesSuccess = false;

          foreach($attributes as $attribute => $value)
          {
            if( property_exists($parameter, $attribute) )
              throw new CHttpException(500, "Свойство ".$attribute." не доступно в классе ".get_class($parameter));

            if( (is_array($value) && in_array($parameter->{$attribute}, $value)) || $parameter->{$attribute} == $value )
            {
              $attributesSuccess = true;
            }
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

      $this->cache[$cacheKey] = $onlyOne ? Arr::reset($parameters) : $parameters;
    }

    return $this->cache[$cacheKey];
  }
}
