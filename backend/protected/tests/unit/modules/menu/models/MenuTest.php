<?php
/**
 * User: Nikita Melnikov <melnikov@shogo.ru>
 * Date: 12/12/12
 */
class MenuTest extends MenuModuleTest
{
  public function testCreate()
  {
    $menu = new BFrontendMenu();

    $this->assertFalse($menu->save());

    $menu->name = 'name';

    $this->assertFalse($menu->save());

    $menu->sysname = 'sys';

    $this->assertTrue($menu->save());
  }

  public function testAddEntry()
  {
    $menuItem = new BFrontendCustomMenuItem();
    $menuItem->name = 'name';
    $menuItem->url  = 'url';
    $menuItem->save();

    $menu = new BFrontendMenu();
    $menu->name = 'name';
    $menu->sysname = 'sysname';

    $this->assertFalse($menu->addEntry($menuItem));

    $menu->save();

    $this->assertTrue($menu->addEntry($menuItem));
  }

  public function testHasMenuEntry()
  {
    $menuItem = new BFrontendCustomMenuItem();
    $menuItem->name = 'name';
    $menuItem->url  = 'url';
    $menuItem->save();

    $menu = new BFrontendMenu();
    $menu->name = 'name';
    $menu->sysname = 'sysname';
    $menu->save();
    $menu->refresh();

    $menu->addEntry($menuItem);

    $this->assertTrue($menu->hasCustomMenuItem($menuItem));
  }

  public function testCreateIndependentRecords()
  {
    $menuItem = new BFrontendCustomMenuItem();
    $menuItem->name = 'name';
    $menuItem->url  = 'url';
    $menuItem->save();

    $menu = new BFrontendMenu();
    $menu->name = 'name';
    $menu->sysname = 'sysname';
    $menu->save();

    $menuEntry = new BFrontendMenuItem();
    $menuEntry->setModel($menuItem);
    $menuEntry->menu_id = $menu->id;

    $this->assertTrue($menuEntry->save());
  }
}