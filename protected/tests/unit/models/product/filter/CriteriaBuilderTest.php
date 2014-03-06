<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.unit.models.product.filter
 */
class CriteriaBuilderTest extends CTestCase
{
  public function testGetFilteredCriteria()
  {
    $filter = $this->getFilter();
    $criteria = new CDbCriteria();
    $productList = new ProductList($criteria, null, false, $filter);
    $productList->getDataProvider();

    $cb = new CriteriaBuilder($criteria);

    //$cb->addCondition($filter->getElementByKey('section_id'));

    $cb->addCondition($filter->getElementByKey('size'));

    $filteredCriteria = $cb->getFilteredCriteria();

    $this->assertContains(2, $filteredCriteria->params);
    $this->assertContains(3, $filteredCriteria->params);
  }

  /**
   * @return ProductFilter
   */
  private function getFilter()
  {
    $filter = new ProductFilter('pf');

    $filter->addElement(array(
      'id' => 'section_id',
      'label' => 'Раздел',
      'type' => 'list',
      'itemLabels' => CHtml::listData(ProductSection::model()->findAll() , 'id', 'name')
      )
      , false
    );

    $size = ProductParameterName::model()->findByAttributes(array('key' => 'size'));

    $filter->addElement(array(
        'key' => 'size',
        'label' => $size->name,
        'type' => 'list',
        'mergeType'  => ProductFilterElement::MERGE_TYPE_OR,
        'itemLabels' => CHtml::listData($size->variants , 'id', 'name')
      )
      , false
    );

    $filter->setState(array(
      //'section_id' => '1',
      '1' => array('2' => '2', '4' => '4')
    ));

    return $filter;
  }
}