<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BRelatedActionDeleteTest extends CDbTestCase
{
  public $fixtures = array(
    'seo_link_section' => 'BLinkSection',
    'seo_link' => 'BLink',
  );

  public function setUp()
  {
    Yii::app()->setUnitEnvironment('seo', 'BLinkSection');
    parent::setUp();
  }

  public function testRun()
  {
    $_POST['id']       = 1;
    $_POST['relation'] = 'links';

    $_SERVER['REQUEST_METHOD']        = 'POST';
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

    $model = BLink::model()->findByPk(1);
    $this->assertNotNull('BActiveRecord', $model);

    $action = new BRelatedActionDelete(Yii::app()->controller, 'delete');
    $action->run();

    $model = BLink::model()->findByPk(1);
    $this->assertNull($model);
  }
}