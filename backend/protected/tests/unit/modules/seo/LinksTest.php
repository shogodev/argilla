<?php

Yii::import('backend.modules.seo.models.*');

class LinksTest extends CDbTestCase
{

  public $fixtures = array(
    'links' => 'BLinks',
  );

  public function tearDown()
  {
    $this->getFixtureManager()->truncateTable('shogocms_links');
  }

  protected function setUp()
  {
    parent::setUp();
  }

  public function testCreateSuccess()
  {
    $link = new BLinks();
    $link->url = '123';
    $link->content = '123';
    $link->title = '123';

    $this->assertTrue($link->save());
  }

  public function testCreateFail()
  {
    $link = new BLinks();

    $this->assertFalse($link->save());
  }

  public function testLenghtFail()
  {
    $item_chr = 'q';
    $item_count = 270;
    $link = new BLinks();
    for ($i = 0; $i < $item_count; $i++)
          $link->url .= $item_chr;
    $link->content = '123';
    $link->title = '123';
    $this->assertFalse($link->save());
  }

  /*public function testDateFail()
  {
    $link = BLinks::model()->find();
    $link->content = '123';
    $link->title = '123';
    $link->url = '123';
    $link->date = '22.22.22';
    $this->assertFalse($link->save());
  }

  public function testDateTrue()
  {
    $link->date = '22.22.22';
    $link = BLinks::model()->find();
  }*/
}
?>
