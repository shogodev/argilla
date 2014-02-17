<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.unit.models.product.filter
 */
Yii::import('frontend.tests.components.FilterTestHelper');

class ProductFilterTest extends CDbTestCase
{
  /**
   * @var FilterTestHelper $fth
   */
  private $fth;

  protected $fixtures = array(
    'product' => 'Product',
    'product_section' => 'ProductSection',
    'product_type' => 'ProductType',
    'product_assignment' => 'ProductAssignment',
    'product_param_name' => 'ProductParameterName',
    'product_param_variant' => 'ProductParameterVariant',
    'product_param' => 'ProductParameter',
  );

  public function setUp()
  {
    parent::setUp();
    Yii::app()->setUnitEnvironment('Product', 'one', array('url' => 'new_product1'));
    $this->fth = new FilterTestHelper();
  }

  public function testProductFilter()
  {
    $this->assertTrue($this->fth->checkFilterForData(
      $this->fth->createFilter(array(
        FilterTestHelper::ELEMENT_SECTION,
        FilterTestHelper::ELEMENT_TYPE,
        FilterTestHelper::ELEMENT_COLOR,
        FilterTestHelper::ELEMENT_SIZE
      )),
      $this->fth->createStateByName(array(
        'section_id' => 'Одежда',
      )),
      array(1, 2, 3, 4, 5)
    ));

    $this->assertTrue($this->fth->checkFilterForData(
      $this->fth->createFilter(array(
        FilterTestHelper::ELEMENT_SECTION,
        FilterTestHelper::ELEMENT_TYPE,
        FilterTestHelper::ELEMENT_COLOR,
        FilterTestHelper::ELEMENT_SIZE
      )),
      $this->fth->createStateByName(array(
        'section_id' => 'Обувь',
      )),
      array(6,7,8,9)
    ));

    $this->assertTrue($this->fth->checkFilterForData(
      $this->fth->createFilter(array(
        FilterTestHelper::ELEMENT_SECTION,
        FilterTestHelper::ELEMENT_TYPE,
        FilterTestHelper::ELEMENT_COLOR,
        FilterTestHelper::ELEMENT_SIZE
      )),
      $this->fth->createStateByName(array(
        'section_id' => 'Одежда',
        'type_id' => 'Теплая',
      )),
      array(1,2)
    ));

    $this->assertTrue($this->fth->checkFilterForData(
      $this->fth->createFilter(array(
        FilterTestHelper::ELEMENT_SECTION,
        FilterTestHelper::ELEMENT_TYPE,
        FilterTestHelper::ELEMENT_COLOR => array('mergeType' => ProductFilterElement::MERGE_TYPE_OR),
        FilterTestHelper::ELEMENT_SIZE => array('mergeType' => ProductFilterElement::MERGE_TYPE_OR)
      )),
      $this->fth->createStateByName(array(
        'section_id' => 'Одежда',
        'type_id' => 'Теплая',
        'Цвет' => array('зеленый'),
        'Размер' => array('10')
      )),
      array(1)
    ));

    $this->assertTrue($this->fth->checkFilterForData(
      $this->fth->createFilter(array(
        FilterTestHelper::ELEMENT_SECTION,
        FilterTestHelper::ELEMENT_TYPE,
        FilterTestHelper::ELEMENT_COLOR ,
        FilterTestHelper::ELEMENT_SIZE => array('mergeType' => ProductFilterElement::MERGE_TYPE_OR)
      )),
      $this->fth->createStateByName(array(
        'Размер' => array('10')
      )),
      array(1, 2, 3)
    ));

    $this->assertTrue($this->fth->checkFilterForData(
      $this->fth->createFilter(array(
        FilterTestHelper::ELEMENT_COLOR => array('mergeType' => ProductFilterElement::MERGE_TYPE_OR) ,
        FilterTestHelper::ELEMENT_SIZE => array('mergeType' => ProductFilterElement::MERGE_TYPE_OR)
      )),
      $this->fth->createStateByName(array(

        'Размер' => array('20', '40')
      )),
      array(2, 3)
    ));

    $this->assertTrue($this->fth->checkFilterForData(
      $this->fth->createFilter(array(
        FilterTestHelper::ELEMENT_COLOR,
        FilterTestHelper::ELEMENT_SIZE => array('mergeType' => ProductFilterElement::MERGE_TYPE_OR),
        FilterTestHelper::ELEMENT_LENGTH
      )),
      $this->fth->createStateByName(array(
        'Цвет' => 'зеленый',
        'Длинна' => 100,
      )),
      array(1, 4)
    ));


    $this->assertTrue($this->fth->checkFilterForData(
    $this->fth->createFilter(array(
      FilterTestHelper::ELEMENT_PRICE,
      FilterTestHelper::ELEMENT_COLOR,
      FilterTestHelper::ELEMENT_LENGTH
    )),
    array('price' => '0-1000'),
    array(3, 5)
  ));

  $this->assertTrue($this->fth->checkFilterForData(
    $this->fth->createFilter(array(
      FilterTestHelper::ELEMENT_PRICE,
      FilterTestHelper::ELEMENT_SECTION,
      FilterTestHelper::ELEMENT_LENGTH
    )),
    Arr::mergeAssoc(
      array('price' => '1001-3000'),
      $this->fth->createStateByName(array(
        'section_id' => 'Одежда'
      ))
    ),
    array(1, 4)
  ));

/* Пока нет реализации фильтрации по полю текст
$this->assertTrue($this->fth->checkFilterForData(
      $this->fth->createFilter(array(
        FilterTestHelper::ELEMENT_COLOR,
        FilterTestHelper::ELEMENT_SIZE => array('type' => ProductFilterElement::MERGE_TYPE_OR),
        FilterTestHelper::ELEMENT_LENGTH,
        FilterTestHelper::ELEMENT_TEXT,
      )),
      array(
        '4' => '111'
      ),
      array(1, 4, 8)
    ));*/

  }

  public function testProductFilterAddElements()
  {
    $productFilter = new ProductFilter('pf');

    $this->filterAddElement($productFilter, 'section_id', 'list', ProductFilterElement::MERGE_TYPE_NONE,ProductSection::model()->findAll(), 'Раздел');
    $this->filterAddElement($productFilter, 'type_id', 'list', ProductFilterElement::MERGE_TYPE_OR, ProductType::model()->findAll(), 'Тип');

    $criteria = new CDbCriteria();
    $criteria->compare('`key`', 'filter');

    $model = new ProductParameterName();
    $parameters = $model->setGroupCriteria($criteria)->search();

    $this->assertNotEmpty($parameters);

    foreach($parameters as $parameter)
    {
      if( $parameter->type == 'text' )
        continue;

      $this->filterAddElement($productFilter, $parameter->id, 'list', ProductFilterElement::MERGE_TYPE_NONE, $parameter->variants, $parameter->name);
    }
  }

  public function testEmptyFilter()
  {
    $productFilter = $this->fth->createFilter(array(
      FilterTestHelper::ELEMENT_SECTION,
      FilterTestHelper::ELEMENT_TYPE,
      FilterTestHelper::ELEMENT_COLOR,
      FilterTestHelper::ELEMENT_SIZE,
    ));

    $criteria = new CDbCriteria();
    $criteria->compare('price', '>0');
    $productList = new ProductList($criteria, null, false, $productFilter);

    $products = $productList->getDataProvider();
    $data = $products->getData();

    $this->assertCount(10, $data);
  }

  public function testFilterSaveStateInSession()
  {
    if( isset($_SESSION['pf']) )
      unset($_SESSION['pf']);

    $state = array(
      'section_id' => 2,
      'type_id' => 4,
      '2' => array(
        '1' => '1',
        '3' => '2'
      )
     );

    $filter = new ProductFilter('pf', true);
    $filter->state = $state;
    $filter->addElement($this->fth->getElement(FilterTestHelper::ELEMENT_SECTION), false);

    $this->assertTrue(isset($_SESSION['pf']));
    $this->assertEquals($state, $_SESSION['pf']);

    if( isset($_SESSION['pf']) )
      unset($_SESSION['pf']);

    $filter = new ProductFilter('pf', false);
    $filter->state = $state;
    $filter->addElement($this->fth->getElement(FilterTestHelper::ELEMENT_SECTION), false);

    $this->assertFalse(isset($_SESSION['pf']));

    if( isset($_SESSION['pf']) )
      unset($_SESSION['pf']);
  }

  public function testFilterLoadStateFromSession()
  {
    $sessionState = array(
      'section_id' => 3,
      '1' => 2
    );

    $_SESSION['pf'] = $sessionState;

    $filter = new ProductFilter('pf', true);
    $filter->addElement($this->fth->getElement(FilterTestHelper::ELEMENT_SECTION), false);
    $this->assertEquals($sessionState, $filter->state);


    $filter = new ProductFilter('pf', false);
    $filter->addElement($this->fth->getElement(FilterTestHelper::ELEMENT_SECTION), false);
    $this->assertNotEquals($sessionState, $filter->state);

    if( isset($_SESSION['pf']) )
      unset($_SESSION['pf']);
  }

  public function testFilterSetStatePartial()
  {
    $firstState = array(
      'section_id' => 3,
      '1' => 2,
    );

    $newState1 = array(
      '2' => array(
        '4' => '4',
        '2' => '2',
        '3' => '3'
      ),
      'type_id' => 1
    );

    $newState2 = array(
      '1' => '',
      'type_id' => null,
      '3' => '4',
      'section_id' => array()
    );

    $filter = new ProductFilter('pf', false);
    $filter->setState($firstState);
    $filter->setStatePartial($newState1);

    $this->assertEquals(Arr::mergeAssoc($firstState, $newState1), $filter->state);

    $filter->setStatePartial($newState2);

    $this->assertEquals(
      array(
        '2' => array(
        '4' => '4',
        '2' => '2',
        '3' => '3'
      ),
      '3' => '4'
    ), $filter->state);
  }

  public function testFilterSetStateAuto()
  {
    $state1 = array(
      'section_id' => '2',
      '3' => array(
        '5'=> '5',
        '2' => '2',
        '3' => '3'
      )
    );

    $state2 = array(
      'section_id' => '1',
      'type_id' => '2',
      '5' => '2'
    );

    $_POST['pf1'] = $state1;
    $_POST['pf1']['submit'] = 1;
    $filter1 = new ProductFilter('pf1', false, true);
    $this->assertEquals($state1, $filter1->state);

    $_GET['pf2'] = $state2;
    $_GET['pf2']['submit'] = 1;
    $filter2 = new ProductFilter('pf2', false);
    $this->assertEquals($state2, $filter2->state);


    $_POST['pf3'] = $state1;
    $_POST['pf3']['submit'] = 1;
    $_GET['pf3'] = $state2;
    $_GET['pf3']['submit'] = 1;
    $filter3 = new ProductFilter('pf3', false, false);
    $this->assertEmpty($filter3->state);
  }

  public function testRemoveElementState()
  {
    $state1 = array(
      'section_id' => '2',
      'category_id' => '1',
      '3' => array(
        '5'=> '5',
        '2' => '2',
        '3' => '3'
      )
    );

    $remove = array(
      'category_id' => '1',
      '3' => array(
        '5' => '5',
        '3' => '3',
      ),
      'remove' => 1
    );

    $_SESSION['pf1'] = $state1;
    $filter1 = new ProductFilter('pf1', true);
    $this->assertEquals($state1, $filter1->state);

    unset($state1['category_id'], $state1['3']['5'], $state1['3']['3']);
    $_POST['pf1'] = $remove;
    $filter1 = new ProductFilter('pf1', true);
    $this->assertEquals($state1, $filter1->state);
  }

  public function testFilterCountAmountNoSelected()
  {
    $this->setUp();

    $filter = $this->fth->createFilter(array(
      FilterTestHelper::ELEMENT_SECTION,
      FilterTestHelper::ELEMENT_TYPE,
      FilterTestHelper::ELEMENT_COLOR,
      FilterTestHelper::ELEMENT_SIZE,
      FilterTestHelper::ELEMENT_LENGTH,
      FilterTestHelper::ELEMENT_PRICE
    ));

    $products = $this->fth->getFilteredData($filter, array());

    $this->assertEquals(10, count($products));

    /** всего 10
     * section_id
     * 1 Одежда 5
     * 2 Обувь 4
     * 3 Оборудование 1
     * type_id
     * 1 Теплая 2
     * 2 Летняя 3
     * 3 Кроссовки 2
     * 4 Ботинки 1
     * 5 Сапоги 1
     * 6 Палатки 1
     * 1
     * 1 Размер 10 3
     * 2 Размер 20 2
     * 3 Размер 30 1
     * 4 Размер 40 1
     * 2
     * 5 Цвет зеленый 2
     * 6 Цвет синий 2
     * 7 Цвет красный 2
     * 3
     * 8 Длинна 100 2
     * 9 Длинна 135 2
     * 10 Длинна 180 2
    */

    $section = $filter->elements[FilterTestHelper::ELEMENT_SECTION]->items;
    $this->assertEquals($section[1]->amount, 5);
    $this->assertEquals($section[2]->amount, 4);
    $this->assertEquals($section[3]->amount, 1);

    $type = $filter->elements[FilterTestHelper::ELEMENT_TYPE]->items;
    $this->assertEquals($type[1]->amount, 2);
    $this->assertEquals($type[2]->amount, 3);
    $this->assertEquals($type[3]->amount, 2);
    $this->assertEquals($type[4]->amount, 1);
    $this->assertEquals($type[5]->amount, 1);
    $this->assertEquals($type[6]->amount, 1);

    $size = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_SIZE)]->items;
    $this->assertEquals($size[1]->amount, 3);
    $this->assertEquals($size[2]->amount, 2);
    $this->assertEquals($size[3]->amount, 1);
    $this->assertEquals($size[4]->amount, 1);

    $color = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_COLOR)]->items;
    $this->assertEquals($color[5]->amount, 2);
    $this->assertEquals($color[6]->amount, 2);
    $this->assertEquals($color[7]->amount, 2);

    $length = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_LENGTH)]->items;
    $this->assertEquals($length[8]->amount, 2);
    $this->assertEquals($length[9]->amount, 2);
    $this->assertEquals($length[10]->amount, 2);

    $price = $filter->elements[FilterTestHelper::ELEMENT_PRICE]->items;
    $this->assertEquals($price['0-1000']->amount, 2);
    $this->assertEquals($price['1001-3000']->amount, 5);
    $this->assertEquals($price['3001-5000']->amount, 1);
    $this->assertEquals($price['5001-999999']->amount, 2);
  }

  public function testFilterCountAmountSelected()
  {
    $this->setUp();

    $filter = $this->fth->createFilter(array(
      FilterTestHelper::ELEMENT_SECTION,
      FilterTestHelper::ELEMENT_TYPE,
      FilterTestHelper::ELEMENT_COLOR,
      FilterTestHelper::ELEMENT_SIZE,
      FilterTestHelper::ELEMENT_LENGTH,
      FilterTestHelper::ELEMENT_PRICE
    ));

    $state = $this->fth->createStateByName(array(
      'section_id' => 'Одежда'
    ));

    $products = $this->fth->getFilteredData($filter, $state);

    $this->assertEquals(5, count($products));

    $section = $filter->elements[FilterTestHelper::ELEMENT_SECTION]->items;
    $this->assertEquals($section[1]->amount, 5);
    $this->assertEquals($section[2]->amount, 4);
    $this->assertEquals($section[3]->amount, 1);

    $type = $filter->elements[FilterTestHelper::ELEMENT_TYPE]->items;
    $this->assertEquals($type[1]->amount, 2);
    $this->assertEquals($type[2]->amount, 3);
    $this->assertEquals($type[3]->amount, 0);
    $this->assertEquals($type[4]->amount, 0);
    $this->assertEquals($type[5]->amount, 0);
    $this->assertEquals($type[6]->amount, 0);

    $size = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_SIZE)]->items;
    $this->assertEquals($size[1]->amount, 3); // 10
    $this->assertEquals($size[2]->amount, 2); // 20
    $this->assertEquals($size[3]->amount, 1); // 30
    $this->assertEquals($size[4]->amount, 1); // 40

    $color = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_COLOR)]->items;
    $this->assertEquals($color[5]->amount, 2); // зел
    $this->assertEquals($color[6]->amount, 2); // синий
    $this->assertEquals($color[7]->amount, 1); // красный

    $length = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_LENGTH)]->items;
    $this->assertEquals($length[8]->amount, 2);  // 100
    $this->assertEquals($length[9]->amount, 2);  // 135
    $this->assertEquals($length[10]->amount, 1); // 180

    $price = $filter->elements[FilterTestHelper::ELEMENT_PRICE]->items;
    $this->assertEquals($price['0-1000']->amount, 2);
    $this->assertEquals($price['1001-3000']->amount, 2);
    $this->assertEquals($price['3001-5000']->amount, 1);
    $this->assertEquals($price['5001-999999']->amount, 0);
  }

  public function testFilterCountAmountSelected2()
  {
    $this->setUp();

    $filter = $this->fth->createFilter(array(
      FilterTestHelper::ELEMENT_SECTION,
      FilterTestHelper::ELEMENT_TYPE,
      FilterTestHelper::ELEMENT_COLOR,
      FilterTestHelper::ELEMENT_SIZE,
      FilterTestHelper::ELEMENT_LENGTH,
      FilterTestHelper::ELEMENT_PRICE
    ));

    $state = $this->fth->createStateByName(array(
      'section_id' => 'Одежда',
      'Размер' => '10'
    ));

    $products = $this->fth->getFilteredData($filter, $state);

    $this->assertEquals(3, count($products));

    $section = $filter->elements[FilterTestHelper::ELEMENT_SECTION]->items;
    $this->assertEquals($section[1]->amount, 3);
    $this->assertEquals($section[2]->amount, 0);
    $this->assertEquals($section[3]->amount, 0);

    $type = $filter->elements[FilterTestHelper::ELEMENT_TYPE]->items;
    $this->assertEquals($type[1]->amount, 2);
    $this->assertEquals($type[2]->amount, 1);
    $this->assertEquals($type[3]->amount, 0);
    $this->assertEquals($type[4]->amount, 0);
    $this->assertEquals($type[5]->amount, 0);
    $this->assertEquals($type[6]->amount, 0);

    $size = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_SIZE)]->items;
    $this->assertEquals($size[1]->amount, 3); // 10
    $this->assertEquals($size[2]->amount, 2); // 20
    $this->assertEquals($size[3]->amount, 1); // 30
    $this->assertEquals($size[4]->amount, 1); // 40

    $color = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_COLOR)]->items;
    $this->assertEquals($color[5]->amount, 1); // зел
    $this->assertEquals($color[6]->amount, 1); // синий
    $this->assertEquals($color[7]->amount, 1); // красный

    $length = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_LENGTH)]->items;
    $this->assertEquals($length[8]->amount, 1);   // 100
    $this->assertEquals($length[9]->amount, 1);   // 135
    $this->assertEquals($length[10]->amount, 1);  // 180

    $price = $filter->elements[FilterTestHelper::ELEMENT_PRICE]->items;
    $this->assertEquals($price['0-1000']->amount, 1);
    $this->assertEquals($price['1001-3000']->amount, 1);
    $this->assertEquals($price['3001-5000']->amount, 1);
    $this->assertEquals($price['5001-999999']->amount, 0);
  }

  public function testFilterCountAmountSelected3()
  {
    $this->setUp();

    $filter = $this->fth->createFilter(array(
      FilterTestHelper::ELEMENT_SECTION,
      FilterTestHelper::ELEMENT_TYPE,
      FilterTestHelper::ELEMENT_COLOR => array('mergeType' => ProductFilterElement::MERGE_TYPE_OR),
      FilterTestHelper::ELEMENT_SIZE,
      FilterTestHelper::ELEMENT_LENGTH,
    ));

    $state = $this->fth->createStateByName(array(
      'section_id' => 'Одежда',
      'Цвет' => array('зеленый')
    ));

    $products = $this->fth->getFilteredData($filter, $state);

    $this->assertEquals(2, count($products));

    $section = $filter->elements[FilterTestHelper::ELEMENT_SECTION]->items;
    $this->assertEquals($section[1]->amount, 2);
    $this->assertEquals($section[2]->amount, 0);
    $this->assertEquals($section[3]->amount, 0);

    $type = $filter->elements[FilterTestHelper::ELEMENT_TYPE]->items;
    $this->assertEquals($type[1]->amount, 1);
    $this->assertEquals($type[2]->amount, 1);
    $this->assertEquals($type[3]->amount, 0);
    $this->assertEquals($type[4]->amount, 0);
    $this->assertEquals($type[5]->amount, 0);
    $this->assertEquals($type[6]->amount, 0);

    $size = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_SIZE)]->items;
    $this->assertEquals($size[1]->amount, 1); // 10
    $this->assertEquals($size[2]->amount, 0); // 20
    $this->assertEquals($size[3]->amount, 1); // 30
    $this->assertEquals($size[4]->amount, 0); // 40

    $color = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_COLOR)]->items;
    $this->assertEquals($color[5]->amount, 2); // зел
    $this->assertEquals($color[6]->amount, 2); // синий
    $this->assertEquals($color[7]->amount, 1); // красный

    $length = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_LENGTH)]->items;
    $this->assertEquals($length[8]->amount, 2);  // 100
    $this->assertEquals($length[9]->amount, 0);  // 135
    $this->assertEquals($length[10]->amount, 0); // 180
  }

  public function testFilterCountAmountSelected4()
  {
    $this->setUp();

    $filter = $this->fth->createFilter(array(
      FilterTestHelper::ELEMENT_SECTION,
      FilterTestHelper::ELEMENT_TYPE,
      FilterTestHelper::ELEMENT_COLOR => array('mergeType' => ProductFilterElement::MERGE_TYPE_OR),
      FilterTestHelper::ELEMENT_SIZE,
      FilterTestHelper::ELEMENT_LENGTH,
      FilterTestHelper::ELEMENT_PRICE
    ));

    $state = $this->fth->createStateByName(array(
      'section_id' => 'Одежда',
      'Цвет' => array('зеленый', 'синий')
    ));

    $products = $this->fth->getFilteredData($filter, $state);

    $this->assertEquals(4, count($products));

    $section = $filter->elements[FilterTestHelper::ELEMENT_SECTION]->items;
    $this->assertEquals($section[1]->amount, 4);
    $this->assertEquals($section[2]->amount, 0);
    $this->assertEquals($section[3]->amount, 0);

    $type = $filter->elements[FilterTestHelper::ELEMENT_TYPE]->items;
    $this->assertEquals($type[1]->amount, 2);
    $this->assertEquals($type[2]->amount, 2);
    $this->assertEquals($type[3]->amount, 0);
    $this->assertEquals($type[4]->amount, 0);
    $this->assertEquals($type[5]->amount, 0);
    $this->assertEquals($type[6]->amount, 0);

    $size = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_SIZE)]->items;
    $this->assertEquals($size[1]->amount, 2); // 10
    $this->assertEquals($size[2]->amount, 1); // 20
    $this->assertEquals($size[3]->amount, 1); // 30
    $this->assertEquals($size[4]->amount, 1); // 40

    $color = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_COLOR)]->items;
    $this->assertEquals($color[5]->amount, 2); // зел
    $this->assertEquals($color[6]->amount, 2); // синий
    $this->assertEquals($color[7]->amount, 1); // красный

    $length = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_LENGTH)]->items;
    $this->assertEquals($length[8]->amount, 2);  // 100
    $this->assertEquals($length[9]->amount, 2);  // 135
    $this->assertEquals($length[10]->amount, 0); // 180

    $price = $filter->elements[FilterTestHelper::ELEMENT_PRICE]->items;
    $this->assertEquals($price['0-1000']->amount, 1);
    $this->assertEquals($price['1001-3000']->amount, 2);
    $this->assertEquals($price['3001-5000']->amount, 1);
    $this->assertEquals($price['5001-999999']->amount, 0);
  }

  public function testFilterCountAmountSelectedRange()
  {
    $this->setUp();

    $filter = $this->fth->createFilter(array(
      FilterTestHelper::ELEMENT_RANGE,
      FilterTestHelper::ELEMENT_SECTION,
      FilterTestHelper::ELEMENT_TYPE,

    ));

    $state = array();

    $products = $this->fth->getFilteredData($filter, $state);

    $this->assertEquals(10, count($products));

    $section = $filter->elements[FilterTestHelper::ELEMENT_SECTION]->items;
    $this->assertEquals($section[1]->amount, 5);
    $this->assertEquals($section[2]->amount, 4);
    $this->assertEquals($section[3]->amount, 1);

    $range = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_RANGE)]->items;
    $this->assertEquals($range['0-5']->amount, 2); // < 6
    $this->assertEquals($range['6-10']->amount, 1); // от 6 до 10
    $this->assertEquals($range['11-30']->amount, 2); // от 11 до 30
  }

  public function testFilterCountAmountSelectedRange2()
  {
    $this->setUp();

    $filter = $this->fth->createFilter(array(
      FilterTestHelper::ELEMENT_RANGE,
      FilterTestHelper::ELEMENT_SECTION,
      FilterTestHelper::ELEMENT_TYPE,
    ));

    $state = array(
      $this->fth->getParameterId(FilterTestHelper::ELEMENT_RANGE) => '0-5',
    );

    $products = $this->fth->getFilteredData($filter, $state);

    $this->assertEquals(2, count($products));

    $range = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_RANGE)]->items;
    $this->assertEquals($range['0-5']->amount, 2); // < 6
    $this->assertEquals($range['6-10']->amount, 1); // от 6 до 10
    $this->assertEquals($range['11-30']->amount, 2); // от 11 до 30

    $section = $filter->elements[FilterTestHelper::ELEMENT_SECTION]->items;
    $this->assertEquals($section[1]->amount, 2);
    $this->assertEquals($section[2]->amount, 0);
    $this->assertEquals($section[3]->amount, 0);
  }

  public function testFilterCountAmountSelectedRange3()
  {
    $this->setUp();

    $filter = $this->fth->createFilter(array(
      FilterTestHelper::ELEMENT_RANGE,
      FilterTestHelper::ELEMENT_COLOR,
      FilterTestHelper::ELEMENT_SECTION,
      FilterTestHelper::ELEMENT_TYPE,
    ));

    $state = array(
      $this->fth->getParameterId(FilterTestHelper::ELEMENT_RANGE) => '0-5',
      $this->fth->getParameterId(FilterTestHelper::ELEMENT_COLOR) => '5'
    );

    $products = $this->fth->getFilteredData($filter, $state);

    $this->assertEquals(1, count($products));

    $color = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_COLOR)]->items;
    $this->assertEquals($color[5]->amount, 1); // зел
    $this->assertEquals($color[6]->amount, 1); // синий
    $this->assertEquals($color[7]->amount, 0); // красный

    $range = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_RANGE)]->items;
    $this->assertEquals($range['0-5']->amount, 1); // < 6
    $this->assertEquals($range['6-10']->amount, 0); // от 6 до 10
    $this->assertEquals($range['11-30']->amount, 1); // от 11 до 30

    $section = $filter->elements[FilterTestHelper::ELEMENT_SECTION]->items;
    $this->assertEquals($section[1]->amount, 1);
    $this->assertEquals($section[2]->amount, 0);
    $this->assertEquals($section[3]->amount, 0);
  }

  public function testFilterCountAmountForRangePrice()
  {
    $this->setUp();

    $filter = $this->fth->createFilter(array(
      FilterTestHelper::ELEMENT_SECTION => array('mergeType' => ProductFilterElement::MERGE_TYPE_OR),
      FilterTestHelper::ELEMENT_TYPE,
      FilterTestHelper::ELEMENT_COLOR,
      FilterTestHelper::ELEMENT_SIZE,
      FilterTestHelper::ELEMENT_LENGTH,
      FilterTestHelper::ELEMENT_PRICE
    ));

    $state = $this->fth->createStateByName(array(
      'section_id' => array('Одежда', 'Обувь'),
    ));

    $state = Arr::mergeAssoc($state, array('price' => '1001-3000'));

    $products = $this->fth->getFilteredData($filter, $state);

    $this->assertEquals(5, count($products));

    $section = $filter->elements[FilterTestHelper::ELEMENT_SECTION]->items;
    $this->assertEquals($section[1]->amount, 2);
    $this->assertEquals($section[2]->amount, 3);
    $this->assertEquals($section[3]->amount, 0);

    $type = $filter->elements[FilterTestHelper::ELEMENT_TYPE]->items;
    $this->assertEquals($type[1]->amount, 1);
    $this->assertEquals($type[2]->amount, 1);
    $this->assertEquals($type[3]->amount, 1);
    $this->assertEquals($type[4]->amount, 1);
    $this->assertEquals($type[5]->amount, 1);
    $this->assertEquals($type[6]->amount, 0);

    $size = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_SIZE)]->items;
    $this->assertEquals($size[1]->amount, 1); // 10
    $this->assertEquals($size[2]->amount, 0); // 20
    $this->assertEquals($size[3]->amount, 1); // 30
    $this->assertEquals($size[4]->amount, 0); // 40

    $color = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_COLOR)]->items;
    $this->assertEquals($color[5]->amount, 2); // зел
    $this->assertEquals($color[6]->amount, 0); // синий
    $this->assertEquals($color[7]->amount, 1); // красный

    $length = $filter->elements[$this->fth->getParameterId(FilterTestHelper::ELEMENT_LENGTH)]->items;
    $this->assertEquals($length[8]->amount, 2);  // 100
    $this->assertEquals($length[9]->amount, 0);  // 135
    $this->assertEquals($length[10]->amount, 1); // 180

    $price = $filter->elements[FilterTestHelper::ELEMENT_PRICE]->items;
    $this->assertEquals($price['0-1000']->amount, 2);
    $this->assertEquals($price['1001-3000']->amount, 5);
    $this->assertEquals($price['3001-5000']->amount, 1);
    $this->assertEquals($price['5001-999999']->amount, 1);
  }

  public function testFilterCheckOldState()
  {
    $filter = $this->fth->createFilter(array(
       FilterTestHelper::ELEMENT_SECTION,
       FilterTestHelper::ELEMENT_TYPE,
       FilterTestHelper::ELEMENT_COLOR,
       FilterTestHelper::ELEMENT_SIZE,
       FilterTestHelper::ELEMENT_PRICE
     ));

    $state = $this->fth->createStateByName(array(
      'section_id' => 'Обувь',
      'type_id' => 'Теплая',
      'Цвет' => 'синий',
      'Длинна' => '100',
    ));

    $state = Arr::mergeAssoc($state, array('price' => '1001-3000'));

    $products = $this->fth->getFilteredData($filter, $state);

    //todo: Отключен пока проблема не решена
    //$this->assertNotEmpty($products);
    //todo: Добавить еще варианты тестирования
  }

  private function filterAddElement(ProductFilter $productFilter, $elementId, $type, $mergeType, $modelForItemLabels, $label)
  {
    $this->assertNotEmpty($modelForItemLabels);

    $productFilter->addElement(
      array(
        'id' => $elementId,
        'label' => $label,
        'type' => $type,
        'mergeType' => $mergeType,
        'itemLabels' => CHtml::listData($modelForItemLabels , 'id', 'name'),
      ), false
    );

    $element = $productFilter->getElementByKey($elementId);
    
    $this->assertInstanceOf('ProductFilterElement'.Utils::ucfirst($type), $element);
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