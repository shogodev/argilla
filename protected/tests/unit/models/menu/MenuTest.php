<?php
class MenuTest extends CDbTestCase
{
  protected $fixtures = array(
    'menu' => 'Menu',
    'menu_item' => 'MenuItem',
    'menu_custom_item' => 'CustomMenuItem',
    'menu_custom_item_data' => 'CustomMenuItemData',
    'info' => 'Info',
  );

  public function testGetMenu()
  {
    $menu = Menu::model()->getMenu('top');
    $this->assertNotEmpty($menu);

    $this->assertEquals('О компании', $menu[0]['label']);
    $this->assertEquals('custom_item', $menu[1]['label']);

    $this->assertArrayHasKey(0, $menu[2]['items']);
    $this->assertEquals('custom_item', $menu[2]['items'][0]['label']);
  }

  public function testSetDepth()
  {
    /**
     * @var Menu $menu
     */
    $menu = Menu::model()->getMenu('top', 2);
    $this->assertNotEmpty($menu[2]['items']);

    $menu = Menu::model()->getMenu('top', 1);
    $this->assertEmpty($menu[2]['items']);
  }
}