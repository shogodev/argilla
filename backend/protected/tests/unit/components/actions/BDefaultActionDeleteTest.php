<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */

Yii::import('backend.models.BAssociation');

class BDefaultActionDeleteTest extends CDbTestCase
{
  public $fixtures = array('news_section' => 'BNewsSection',
                           'info'         => 'BInfo');

  public function setUp()
  {
    parent::setUp();
    Yii::app()->setAjaxRequest();
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
    Yii::app()->setUnitEnvironment('Info', 'BInfo', 'update', array('id' => '3'));
    $action = Yii::createComponent(array('class' => 'BDefaultActionDelete',
                                         'model' => Yii::app()->controller->loadModel(Yii::app()->request->getParam('id'))),
                                   Yii::app()->controller, 'delete');
    $action->run();

    $model = BInfo::model()->findByPk(3);
    $this->assertNull($model);
  }
}