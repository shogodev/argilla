<?php

class CActiveDataProviderTest extends CTestCase
{
  public function setUp()
  {
    Yii::import('backend.modules.news.models.*');
    Yii::import('backend.modules.news.controllers.*');
  }

  public function testCounstuctor()
  {
    $dataProvider = new BActiveDataProvider('BNews', array());
    $itemsPerPage = $dataProvider->getCurrentPageSize();

    $this->assertEquals($itemsPerPage, BActiveDataProvider::PAGINATION_PAGE_SIZE);
  }

  public function testCustomElements()
  {
    $dataProvider = new BActiveDataProvider('BNews', array());

    $elements = array(10000000 => 'Все',
                      5        => 5,
                      10       => 10,
                      15       => 10,
                      50       => 50,
                     );

    $dataProvider->setPageSizeElements($elements);

    $this->assertEquals($dataProvider->getPageSizeFormElements(), $elements);
  }

  public function testForm()
  {
    Yii::app()->controller             = new BNewsController('news');
    Yii::app()->controller->action     = new CInlineAction(Yii::app()->controller, 'index');

    $dataProvider = new BActiveDataProvider('BNews', array());
    $dataProvider->getPageSizeForm();
  }
}