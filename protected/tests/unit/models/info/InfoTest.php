<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.models.info
 */
class InfoTest extends CDbTestCase
{
  protected $fixtures = array('info' => 'Info');

  public function setUp()
  {
    Yii::app()->setUnitEnvironment('Info', 'index', array('url' => 'o_kompanii'));

    parent::setUp();
  }

  public function testGetSiblings()
  {
    /**
     * @var Info $model
     */
    $model = Info::model()->findByPk(2);
    $this->assertCount(2, $model->getSiblings());

    /**
     * @var Info $model
     */
    $model = Info::model()->findByPk(5);
    $this->assertCount(1, $model->getSiblings());

    /**
     * @var Info $model
     */
    $model = Info::model()->findByPk(5);
    $this->assertCount(2, $model->getSiblings(true));
  }
}