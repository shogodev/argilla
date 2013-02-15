<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 19.09.12
 */
class ProductControllerTest extends CDbTestCase
{
  public $fixtures = array('product'                 => 'BProduct',
                           'product_type'            => 'BProductType',
                           'product_section'         => 'BProductSection',
                           'product_assignment'      => 'BProductAssignment',
                           'product_tree_assignment' => 'BProductTreeAssignment');

  public function setUp()
  {
    parent::setUp();
  }

  public function testActionUpdateAssignment()
  {
    $_POST['BProductAssignment']['attribute'] = 'section_id';
    $_POST['BProductAssignment']['depended']  = 'type_id';
    $_POST['BProductAssignment']['value']     = '1';

    $controller = new BProductController('product');
    $controller->actionUpdateAssignment(1);

    $this->expectOutputRegex("/prod_type1.*prod_type2.*prod_type3<\/option>$/");
  }
}