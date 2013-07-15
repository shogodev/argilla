<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
yii::import('backend.models.*');

class BProductCopierTest extends CDbTestCase
{
  protected $fixtures = array(
    'product' => 'BProduct',
    'product_assignment' => 'BProductAssignment',
    'association' => 'BAssociation',
  );

  protected function setUp()
  {
    parent::setUp();
  }

  public function testCopy()
  {
    $copier = new BProductCopier(15);
    $copyId = $copier->copy();

    $model = BProduct::model()->findByPk($copyId);

    $this->assertNotNull($model);
    $this->assertEquals(444, Arr::reset($model->assignment)->section_id);
    $this->assertEquals(150, Arr::reset($model->associations)->dst_id);
  }
}