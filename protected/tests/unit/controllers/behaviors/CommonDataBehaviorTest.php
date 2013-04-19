<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.controllers.behaviors
 */
class CommonDataBehaviorTest extends CTestCase
{
  protected $fixtures = array('contact_group' => 'ContactGroup');

  public function setUp()
  {
    Yii::app()->setUnitEnvironment('Index', 'index');

    parent::setUp();
  }

  public function testGetContacts()
  {
    $contacts = Yii::app()->controller->contacts;

    $this->assertNotEmpty($contacts['phones']);
    $this->assertContains('8 800 000 00 00', $contacts['phones']);

    $contacts = Yii::app()->controller->getContacts('phones');
    $this->assertNotEmpty($contacts);
    $this->assertContains('8 800 300 40 50', $contacts);

    $contacts = Yii::app()->controller->getContacts('icq');
    $this->assertEmpty($contacts);
  }
}