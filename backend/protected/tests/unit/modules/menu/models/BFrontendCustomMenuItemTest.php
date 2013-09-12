<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BFrontendCustomMenuItemTest extends MenuModuleTest
{
  public function testCreate()
  {
    $item = new BFrontendCustomMenuItem();

    $this->assertFalse($item->save());

    $item->name = 'name';
    $item->url  = 'url';

    $this->assertTrue($item->save());
  }

  public function testAppendData()
  {
    $item = new BFrontendCustomMenuItem();
    $item->name = 'name';
    $item->url  = 'url';
    $item->save();

    $data = array(
      array(
        'name' => '123',
        'value' => '123',
      ),
      array(
        'name' => '321',
        'value' => '321',
      ),
    );

    $item->appendData($data);

    $this->assertEquals(CHtml::listData($item->data, 'name', 'value'), array(123 => 123, 321 => 321));

    $item->clearData();
    $item->refresh();

    $this->assertEquals($item->data, array());
  }
}