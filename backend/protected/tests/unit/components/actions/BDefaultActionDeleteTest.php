<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */

Yii::import('backend.models.BAssociation');

class BDefaultActionDeleteTest extends CDbTestCase
{
  protected $fixtures = array('news_section' => 'BNewsSection',
                           'info'         => 'BInfo');

  public function setUp()
  {
    Yii::app()->setAjaxRequest();
    parent::setUp();
  }

  /**
   * @expectedException CHttpException
   */
  public function testPostOnlyException()
  {
    $_SERVER['REQUEST_METHOD'] = '';
    Yii::app()->setUnitEnvironment('News', 'BNewsSection');
    $action = new BDefaultActionDelete(Yii::app()->controller, 'delete');
    $action->run();
  }

  public function testDelete()
  {
    Yii::app()->setUnitEnvironment('News', 'BNewsSection', 'update', array('id' => '3'));

    $action = Yii::createComponent(
      array(
        'class' => 'BDefaultActionDelete',
        'model' => Yii::app()->controller->loadModel(Yii::app()->request->getParam('id'))
      ),
      Yii::app()->controller, 'delete'
    );
    $action->run();

    $model = BNewsSection::model()->findByPk(3);
    $this->assertNull($model);
  }

  public function testDeleteNested()
  {
    Yii::app()->setUnitEnvironment('Info', 'BInfo', 'update', array('id' => '3'));

    $action = Yii::createComponent(
      array(
        'class' => 'BDefaultActionDelete',
        'model' => Yii::app()->controller->loadModel(Yii::app()->request->getParam('id'))
      ),
      Yii::app()->controller, 'delete'
    );
    $action->run();

    $model = BInfo::model()->findByPk(3);
    $this->assertNull($model);
  }
}