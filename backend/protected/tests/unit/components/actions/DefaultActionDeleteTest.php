<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 28.08.12
 */
class DefaultActionDeleteTest extends CDbTestCase
{
  public $fixtures = array('news_section' => 'NewsSection',
                           'info'         => 'BInfo');


  public function setUp()
  {
    parent::setUp();

    $_SERVER['REQUEST_METHOD']        = 'POST';
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
  }

  public function testDelete()
  {
    Yii::app()->setUnitEnvironment('News', 'BNewsSection', 'update', array('id' => '3'));
    $action = Yii::createComponent(array('class' => 'BDefaultActionDelete',
                                         'model' => Yii::app()->controller->loadModel(Yii::app()->request->getParam('id'))),
                                   Yii::app()->controller, 'delete');
    $action->run();

    $model = BNewsSection::model()->findByPk(3);
    $this->assertNull($model);
  }

  public function testDeleteNested()
  {
    Yii::app()->setUnitEnvironment('BInfo', 'BInfo', 'update', array('id' => '3'));
    $action = Yii::createComponent(array('class' => 'BDefaultActionDelete',
                                         'model' => Yii::app()->controller->loadModel(Yii::app()->request->getParam('id'))),
                                   Yii::app()->controller, 'delete');
    $action->run();

    $model = BInfo::model()->findByPk(3);
    $this->assertNull($model);
  }

}
?>