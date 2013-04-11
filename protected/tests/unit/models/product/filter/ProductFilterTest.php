<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.unit.models.product.filter
 */
class ProductFilterTest extends CDbTestCase
{
  const ELEMENT_SECTION = 'section';
  const ELEMENT_TYPE = 'type';
  const ELEMENT_SIZE = 'size';
  const ELEMENT_COLOR = 'color';
  const ELEMENT_LENGTH = 'length';

  /**
   * @var FilterTestHelper $filterTestHelper
   */
  private $filterTestHelper;

  private $filterElements;

  public $fixtures = array(
    'product' => 'Product',
    'product_section' => 'ProductSection',
    'product_type' => 'ProductType',
    'product_assignment' => 'ProductAssignment',
    'product_param_name' => 'ProductParamName',
    'product_param_variant' => 'ProductParamVariant',
    'product_param' => 'ProductParam',
  );

  public function setUp()
  {
    Yii::import('frontend.tests.components.FilterTestHelper');

    $this->filterTestHelper = new FilterTestHelper();

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

    $size = ProductParamName::model()->findByAttributes(array('key' => 'size'));

    $this->filterElements[self::ELEMENT_SIZE] = array(
      'id' => $size->id,
      'label' => $size->name,
      'type' => 'list',
      'itemLabels' => CHtml::listData($size->variants , 'id', 'name'),
    );

    $color = ProductParamName::model()->findByAttributes(array('key' => 'color'));

    $this->filterElements[self::ELEMENT_COLOR] = array(
      'id' => $color->id,
      'label' => $color->name,
      'type' => 'list',
      'itemLabels' => CHtml::listData($color->variants , 'id', 'name'),
    );

    $length = ProductParamName::model()->findByAttributes(array('key' => 'length'));

    $this->filterElements[self::ELEMENT_LENGTH] = array(
      'id' => $length->id,
      'label' => $length->name,
      'type' => 'list',
      'itemLabels' => CHtml::listData($length->variants , 'id', 'name'),
    );

    parent::setUp();
  }

  public function testProductFilter()
  {
    $this->assertTrue($this->checkFilterForData(
      $this->createFilter(array(
        self::ELEMENT_SECTION,
        self::ELEMENT_TYPE,
        self::ELEMENT_COLOR,
        self::ELEMENT_SIZE
      )),
      $this->filterTestHelper->createStateByName(array(
        'section_id' => 'Одежда',
      )),
      array(1, 2, 3, 4, 5)
    ));

    $this->assertTrue($this->checkFilterForData(
      $this->createFilter(array(
        self::ELEMENT_SECTION,
        self::ELEMENT_TYPE,
        self::ELEMENT_COLOR,
        self::ELEMENT_SIZE
      )),
      $this->filterTestHelper->createStateByName(array(
        'section_id' => 'Обувь',
      )),
      array(6,7,8,9)
    ));

    $this->assertTrue($this->checkFilterForData(
      $this->createFilter(array(
        self::ELEMENT_SECTION,
        self::ELEMENT_TYPE,
        self::ELEMENT_COLOR,
        self::ELEMENT_SIZE
      )),
      $this->filterTestHelper->createStateByName(array(
        'section_id' => 'Одежда',
        'type_id' => 'Теплая',
      )),
      array(1,2)
    ));

    $this->assertTrue($this->checkFilterForData(
      $this->createFilter(array(
        self::ELEMENT_SECTION,
        self::ELEMENT_TYPE,
        self::ELEMENT_COLOR => array('type' => 'multipleOr'),
        self::ELEMENT_SIZE => array('type' => 'multipleOr')
      )),
      $this->filterTestHelper->createStateByName(array(
        'section_id' => 'Одежда',
        'type_id' => 'Теплая',
        'Цвет' => array('зеленый'),
        'Размер' => array('10')
      )),
      array(1)
    ));

    $this->assertTrue($this->checkFilterForData(
      $this->createFilter(array(
        self::ELEMENT_SECTION,
        self::ELEMENT_TYPE,
        self::ELEMENT_COLOR ,
        self::ELEMENT_SIZE => array('type' => 'multipleOr')
      )),
      $this->filterTestHelper->createStateByName(array(
        'Размер' => array('10')
      )),
      array(1, 2, 3)
    ));

    $this->assertTrue($this->checkFilterForData(
      $this->createFilter(array(
        self::ELEMENT_COLOR => array('type' => 'multipleOr') ,
        self::ELEMENT_SIZE => array('type' => 'multipleOr')
      )),
      $this->filterTestHelper->createStateByName(array(

        'Размер' => array('20', '40')
      )),
      array(2, 3)
    ));

    $this->assertTrue($this->checkFilterForData(
      $this->createFilter(array(
        self::ELEMENT_COLOR,
        self::ELEMENT_SIZE => array('type' => 'multipleOr'),
        self::ELEMENT_LENGTH
      )),
      $this->filterTestHelper->createStateByName(array(
        'Цвет' => 'зеленый',
        'Длинна' => 100,
      )),
      array(1, 4)
    ));

  }

  public function testProductFilterAddElements()
  {
    $productFilter = new ProductFilter('pf');

    $this->filterAddElement($productFilter, 'section_id', 'list', ProductSection::model()->findAll(), 'Раздел');
    $this->filterAddElement($productFilter, 'type_id', 'multipleOr', ProductType::model()->findAll(), 'Тип');

    $criteria = new CDbCriteria();
    $criteria->compare('`key`', 'filter');

    $parameters = ProductParam::model()->getParameters($criteria);

    $this->assertNotEmpty($parameters);

    foreach($parameters as $parameter)
    {
      if( $parameter->type == 'text' )
        continue;

      $this->filterAddElement($productFilter, $parameter->id, 'radio', $parameter->variants, $parameter->name);
    }
  }

  public function testEmptyFilter()
  {
    $productFilter = $this->createFilter(array(
      self::ELEMENT_SECTION,
      self::ELEMENT_TYPE,
      self::ELEMENT_COLOR,
      self::ELEMENT_SIZE,
    ));

    $criteria = new CDbCriteria();
    $productList = new ProductList($criteria, null, false, $productFilter);

    $products = $productList->getProducts();
    $data = $products->getData();

    $this->assertCount(10, $data);
  }

  private function checkFilterForData($filter, $state, $assertProductIds)
  {
    $products = $this->getFilteredData($filter, $state);

    $this->assertNotEmpty($products);

    $productIds = array();

    foreach($products as $product)
      $productIds[] = $product->id;

    sort($productIds);
    sort($assertProductIds);

    return $productIds == $assertProductIds;
  }

  private function createFilter($elements)
  {
    $productFilter = new ProductFilter('pf');

    foreach($elements as $key => $element)
    {
      $productFilter->addElement(
        is_array($element) ? CMap::mergeArray($this->filterElements[$key], $element) : $this->filterElements[$element]
       , false
      );
    }

    return $productFilter;
  }

  /**
   * @var Product[] $data
   */
  private function getFilteredData($filter, $state)
  {
    $filter->setState( $state );

    $criteria = new CDbCriteria();
    $productList = new ProductList($criteria, null, false, $filter);

    $products = $productList->getProducts();
    /**
     * @var Product[] $data
     */
    $data = $products->getData();

    return $data;
  }

  private function filterAddElement($productFilter, $elementId, $type, $modelForItemLabels, $label)
  {
    $this->assertNotEmpty($modelForItemLabels);

    $productFilter->addElement(
      array(
        'id' => $elementId,
        'label' => $label,
        'type' => $type,
        'itemLabels' => CHtml::listData($modelForItemLabels , 'id', 'name'),
      ), false
    );

    $element = $productFilter->elements[$elementId];

    $this->assertInstanceOf('ProductFilterElement'.Utils::ucfirst($type),  $element);
    $this->assertEquals($label, $element->label);

    foreach($modelForItemLabels as $section)
    {
      $itemLabel = $element->itemLabels[$section->id];
      $this->assertNotEmpty($itemLabel);
      $this->assertEquals($section->name, $itemLabel);
    }

    $this->assertNotEmpty($productFilter->getElementByKey($elementId));
  }
}