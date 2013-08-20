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
  const ELEMENT_SECTION = 'section_id';
  const ELEMENT_TYPE = 'type_id';
  const ELEMENT_SIZE = 'param_1';
  const ELEMENT_COLOR = 'param_2';
  const ELEMENT_LENGTH = 'param_3';
  const ELEMENT_TEXT = 'param_4';
  const ELEMENT_PRICE = 'price';
  const ELEMENT_RANGE = 'param_10';

  protected $parameters;

  protected $filterElements;

  public function __construct()
  {
    $this->filterElements[self::ELEMENT_SECTION] = array(
      'id' => 'section_id',
      'label' => 'Раздел',
      'type' => 'list',
      'itemLabels' => CHtml::listData(ProductSection::model()->findAll() , 'id', 'name'),
    );

    $this->filterElements[self::ELEMENT_TYPE] = array(
      'id' => 'type_id',
      'label' => 'Тип',
      'type' => 'list',
      'itemLabels' => CHtml::listData(ProductType::model()->findAll() , 'id', 'name'),
    );

    $size = ProductParameterName::model()->findByAttributes(array('key' => 'size'));

    $this->filterElements[self::ELEMENT_SIZE] = array(
      'id' => $size->id,
      'label' => $size->name,
      'type' => 'list',
      'itemLabels' => CHtml::listData($size->variants , 'id', 'name'),
    );

    $color = ProductParameterName::model()->findByAttributes(array('key' => 'color'));

    $this->filterElements[self::ELEMENT_COLOR] = array(
      'id' => $color->id,
      'label' => $color->name,
      'type' => 'list',
      'itemLabels' => CHtml::listData($color->variants , 'id', 'name'),
    );

    $length = ProductParameterName::model()->findByAttributes(array('key' => 'length'));

    $this->filterElements[self::ELEMENT_LENGTH] = array(
      'id' => $length->id,
      'label' => $length->name,
      'type' => 'list',
      'itemLabels' => CHtml::listData($length->variants , 'id', 'name'),
    );

    $text = ProductParameterName::model()->findByAttributes(array('key' => 'text'));
    $this->filterElements[self::ELEMENT_TEXT] = array(
      'id' => $text->id,
      'label' => $text->name,
      'type' => 'text',
    );

    $this->filterElements[self::ELEMENT_PRICE] = array(
      'id' => 'price',
      'label' => 'Цена',
      'type' => 'range',
      'itemLabels' => array(
        '0-1000' => '< 1000',
        '1001-3000' => 'от 1000 до 3000',
        '3001-5000' => 'от 3000 до 5000',
        '5001-999999' => ' > 5000',
      ),
    );

    $range = ProductParameterName::model()->findByAttributes(array('key' => 'range'));
    $this->filterElements[self::ELEMENT_RANGE] = array(
      'id' => $range->id,
      'label' => $range->name,
      'type' => 'range',
      'itemLabels' => array(
        '0-5' => '< 6',
        '6-10' => 'от 6 до 10',
        '11-30' => 'от 11 до 30',
      ),
    );

  }

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

  public function createFilter($elements)
  {
    $productFilter = new ProductFilter('pf');

    foreach($elements as $key => $element)
    {
      $productFilter->addElement(
        is_array($element) ? CMap::mergeArray($this->filterElements[$key], $element) : $this->filterElements[$element],
        false
      );
    }

    return $productFilter;
  }

  /**
   * @param ProductFilter $filter
   * @param $state
   * @return Product[]
   */
  public function getFilteredData(ProductFilter $filter, $state)
  {
    $filter->setState( $state );

    $criteria = new CDbCriteria();
    $criteria->compare('price', '>0');
    $productList = new ProductList($criteria, null, false, $filter);

    $products = $productList->getProducts();
    /**
     * @var Product[] $data
     */
    $data = $products->getData();

    return $data;
  }

  public function checkFilterForData($filter, $state, $assertProductIds)
  {
    $products = $this->getFilteredData($filter, $state);

    $productIds = array();

    foreach($products as $product)
      $productIds[] = $product->id;

    sort($productIds);
    sort($assertProductIds);

    return $productIds == $assertProductIds;
  }

  public function getElement($elementKey)
  {
    return $this->filterElements[$elementKey];
  }

  public function getParameterId($index)
  {
    return str_replace('param_', '', $index);
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
      $item = ProductParameterVariant::model()->findByAttributes(array('param_id' => $paramId, 'name' => $value));

      if( !$item )
        throw new ErrorException('elementName does not find in ProductParameterVariant');

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

      $model = new ProductParameterName();
      $this->parameters = $model->setGroupCriteria($criteria)->search();
    }

    return $this->parameters;
  }
}