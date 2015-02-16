<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BSaveAssociationActionTest extends CDbTestCase
{
  protected $fixtures = array(
    'product' => 'BProduct',
  );

  public function setUp()
  {
    Yii::app()->setUnitEnvironment('product', 'BProduct');
    Yii::app()->setAjaxRequest();

    parent::setUp();
  }

  /**
   * @expectedException CHttpException
   */
  public function testPostOnlyException()
  {
    $_SERVER['HTTP_X_REQUESTED_WITH'] = '';
    Yii::app()->setUnitEnvironment('seo', 'BLink');
    $action = new BSaveAssociationAction(Yii::app()->controller, 'delete');
    $action->run('BProduct', 11, 'BProduct');
  }

  public function testRunOne()
  {
    $this->getFixtureManager()->truncateTable('{{association}}');
    $model = BProduct::model()->findByPk(11);
    $this->assertEmpty($model->associations);

    $action = new BSaveAssociationAction(Yii::app()->controller, 'saveAssociations');

    $_POST['ids']   = 12;
    $_POST['value'] = 1;
    $action->run('BProduct', 11, 'BProduct');

    $this->assertEquals(12, Arr::reset($model->getRelated('associations', true))->dst_id);
  }

  public function testRunMany()
  {
    $this->getFixtureManager()->truncateTable('{{association}}');
    $action = new BSaveAssociationAction(Yii::app()->controller, 'saveAssociations');

    $_POST['ids']   = array(12, 13);
    $_POST['value'] = 1;
    $action->run('BProduct', 11, 'BProduct');

    $model = BProduct::model()->findByPk(11);
    $this->assertEquals(12, Arr::reset($model->associations)->dst_id);
    $this->assertEquals(13, Arr::end($model->associations)->dst_id);
  }

  public function testRunDelete()
  {
    $this->getFixtureManager()->truncateTable('{{association}}');
    $action = new BSaveAssociationAction(Yii::app()->controller, 'saveAssociations');

    $_POST['ids']   = 12;
    $_POST['value'] = 1;
    $action->run('BProduct', 11, 'BProduct');

    $model = BProduct::model()->findByPk(11);
    $this->assertEquals(12, Arr::reset($model->associations)->dst_id);

    $_POST['ids']   = 12;
    $_POST['value'] = 0;
    $action->run('BProduct', 11, 'BProduct');

    $this->assertEmpty($model->getRelated('associations', true));
  }
}
