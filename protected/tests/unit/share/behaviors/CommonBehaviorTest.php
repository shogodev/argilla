<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.unit.share.behaviors
 */
class CommonBehaviorTest extends CDbTestCase
{
  protected $fixtures = array(
    'contact_field' => 'ContactField',
    'contact_group' => 'ContactGroup',
    'text_block' => 'TextBlock',
    'info' => 'Info',
    'settings' => 'Settings',
  );

  public function setUp()
  {
    Yii::app()->setUnitEnvironment('Index', 'index');

    parent::setUp();
  }

  public function testHeaderContacts()
  {
    $contacts = Yii::app()->controller->getHeaderContacts();

    $contact = Arr::reduce($contacts);

    $this->assertNotEmpty($contact->getFields('phones'));
    $this->assertContains('8 800 000 00 00', $contact->getFields('phones')[0]);
    $this->assertContains('8 800 300 40 50', $contact->getFields('phones')[1]);
    $this->assertEmpty($contact->getFields('icq'));
  }

  public function testGetSettings()
  {
    $settings = Yii::app()->controller->settings;
    $this->assertEquals('12', $settings['product_page_size']);
    $this->assertEquals('10', $settings['page_size']);

    $settings = Yii::app()->controller->getSettings('product_page_size');
    $this->assertEquals('12', $settings);
  }
}