<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.unit.compontnts
 */
class FCollectionUITest extends CTestCase
{
  public function testButtonAdd()
  {
    $collection = new FCollectionUI('test');

    $button = $collection->buttonAdd(array('id' => 1, 'type' => 'product'), 'Добавить', array('class' => 'btn'), FCollectionUI::BT_ADD_ALWAYS);
    $this->assertContains('data-id="1"', $button);
    $this->assertContains('data-type="product"', $button);
    $this->assertContains('>Добавить</a>', $button);
    $this->assertContains('class="btn to-test"', $button);

    $button = $collection->buttonAdd(array('id' => 1, 'type' => 'product', 'data-amount' => 10), '', array(), FCollectionUI::BT_ADD_ALWAYS);
    $this->assertContains('data-amount="10"', $button);

    $product = new Product();
    $product->id = 2;
    $button = $collection->buttonAdd($product, '', array(), FCollectionUI::BT_ADD_ALWAYS);
    $this->assertContains('data-id="2"', $button);
    $this->assertContains('data-type="product"', $button);
  }

  public function testButtonAddTypeAddAlways()
  {
    $collection = new FCollectionUI('test');

    $button = $collection->buttonAdd(array('id' => 2, 'type' => 'product'), array('Добавить', 'Удалить'), array(), FCollectionUI::BT_ADD_ALWAYS);
    $this->assertContains('>Добавить</a>', $button);

    $collection->add(array('id' => 2, 'type' => 'product'));
    $button = $collection->buttonAdd(array('id' => 2, 'type' => 'product'), array('Добавить', 'Удалить'), array(), FCollectionUI::BT_ADD_ALWAYS);
    $this->assertContains('>Удалить</a>', $button);
  }

  public function testButtonAddTypeAddOnce()
  {
    $collection = new FCollectionUI('test');
    $collection->add(array('id' => 2, 'type' => 'product'));

    $button = $collection->buttonAdd(array('id' => 1, 'type' => 'product'), array('Добавить', 'Удалить'), array() ,FCollectionUI::BT_ADD_ONCE);
    $this->assertContains('>Добавить</a>', $button);
    $this->assertContains('data-do-not-add="0"', $button);
    $this->assertContains('data-not-added-text="Добавить"', $button);
    $this->assertContains('data-added-text="Удалить"', $button);

    $button = $collection->buttonAdd(array('id' => 2, 'type' => 'product'), array('Добавить', 'Удалить'), array(), FCollectionUI::BT_ADD_ONCE);
    $this->assertContains('data-do-not-add="1"', $button);
    $this->assertContains('>Удалить</a>', $button);
    $this->assertContains('class="to-test already-in-test"', $button);
  }

  public function testButtonAddTypeToggle()
  {
    $collection = new FCollectionUI('test');

    $button = $collection->buttonAdd(array('id' => 2, 'type' => 'product'), array('Добавить', 'Удалить'), array(), FCollectionUI::BT_TOGGLE);
    $this->assertContains('>Добавить</a>', $button);
    $this->assertContains('data-do-not-add="0"', $button);
    $this->assertContains('data-remove-toggle="0"', $button);
    $this->assertContains('class="to-test"', $button);

    $collection->add(array('id' => 2, 'type' => 'product'));

    $button = $collection->buttonAdd(array('id' => 2, 'type' => 'product'), array('Добавить', 'Удалить'), array(), FCollectionUI::BT_TOGGLE);
    $this->assertContains('>Удалить</a>', $button);
    $this->assertContains('data-do-not-add="1"', $button);
    $this->assertContains('data-remove-toggle="1"', $button);
    $this->assertContains('class="to-test already-in-test"', $button);
  }
} 