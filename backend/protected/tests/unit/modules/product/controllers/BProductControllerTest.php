<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BProductControllerTest extends CDbTestCase
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
    $_POST['BProduct']['attribute'] = 'section_id';
    $_POST['BProduct']['inputs']    = array('type_id' => array('type' => 'dropdown'));
    $_POST['BProduct']['value']     = '1';

    $controller = new BProductController('product');
    $controller->actionUpdateAssignment(1);

    $output = $this->getActualOutput();
    $output = CJavaScript::jsonDecode($output);

    $this->assertRegExp("/prod_type1.*prod_type2.*prod_type3<\/option>$/", $output['type_id']);
  }
}