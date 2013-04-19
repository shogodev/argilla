<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */

Yii::import('backend.modules.product.controllers.*');
Yii::import('backend.modules.product.models.*');

class BCustomColumnsGridViewTest extends CDbTestCase
{
  protected $fixtures = array(
    'settings_grid' => 'BGridSettings',
  );

  public function setUp()
  {
    Yii::app()->setUnitEnvironment('Product', 'BProduct', 'index');
    parent::setUp();
  }

  public function testInit()
  {
    $model      = new BProduct();
    $properties = array(
      'dataProvider' => $model->search(),
      'filter' => $model,
      'columns' => array(
        array(
          'name' => 'BProduct',
          'class' => 'BAssociationColumn'
        ),
      ),
    );

    /**
     * @var BCustomColumnsGridView $widget
     */
    $widget = Yii::app()->getWidgetFactory()->createWidget(Yii::app()->controller, 'BCustomColumnsGridView', $properties);
    $widget->init();

    $classes = array_flip(array_map(function($obj){
      return get_class($obj);
    }, $widget->columns));

    $this->assertCount(4, $widget->columns);
    $this->assertArrayHasKey('BButtonColumn', $classes);
    $this->assertArrayHasKey('BPkColumn', $classes);
    $this->assertArrayHasKey('OnFlyEditField', $classes);
    $this->assertArrayHasKey('BAssociationColumn', $classes);
    $this->assertArrayNotHasKey('BEditColumn', $classes);
  }
}