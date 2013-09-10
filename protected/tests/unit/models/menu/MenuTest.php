<?php
class MenuTest extends CDbTestCase
{
  protected $fixtures = array(
    'menu' => 'Menu',
    'menu_item' => 'MenuItem',
    'menu_custom_item' => 'CustomMenuItem',
    'info' => 'Info',
  );

  public function testBuild()
  {
    $menu = Menu::model()->getMenu('top');
    $this->assertNotEmpty($menu);

    $this->assertEquals('О компании', $menu[0]['label']);
    $this->assertEquals('custom_item', $menu[1]['label']);

    $this->assertArrayHasKey(0, $menu[2]['items']);
    $this->assertEquals('custom_item', $menu[2]['items'][0]['label']);
  }
}