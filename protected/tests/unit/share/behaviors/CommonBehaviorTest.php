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

  public function testTextBlock()
  {
    $textBlock = Yii::app()->controller->textBlock('main');
    $this->assertNotEmpty($textBlock);
    $this->assertEquals($textBlock->content, 'Текст 2');

    $textBlock = Yii::app()->controller->textBlock('mainNotVisible');
    $this->assertEmpty($textBlock);

    $textBlock = Yii::app()->controller->textBlock('doesNotExist');
    $this->assertEmpty($textBlock);
  }

  public function testTextBlocks()
  {
    $textBlocks = Yii::app()->controller->textBlocks('main');
    $this->assertCount(2, $textBlocks);
    $this->assertEquals($textBlocks[0]->content, 'Текст 2');
    $this->assertEquals($textBlocks[1]->content, 'Текст 1');

    $textBlocks = Yii::app()->controller->textBlock('mainNotVisible');
    $this->assertEmpty($textBlocks);

    $textBlocks = Yii::app()->controller->textBlocks('doesNotExist');
    $this->assertEmpty($textBlocks);
  }

  public function testTextBlockRegister()
  {
    $textBlock = new TextBlock();
    $textBlock->attributes = array(
      'location' => 'index/index',
      'content' => 'test',
      'visible' => '1'
    );
    $textBlock->save();

    $this->clearTextBlocksCache();

    Yii::app()->controller->textBlockRegister(null, 'new content');
    $textBlock = TextBlock::model()->findByAttributes(array('location' => 'index/index'));
    $this->assertRegExp('/test/iu', $textBlock->content);

    TextBlock::model()->deleteAllByAttributes(array('location' => 'index/index'));
    $this->clearTextBlocksCache();

    Yii::app()->controller->textBlockRegister();
    $textBlock = TextBlock::model()->findByAttributes(array('location' => 'index/index'));
    $this->assertRegExp('/данный текстовый блок сгенерирован автоматическ/iu', $textBlock->content);

    $this->clearTextBlocksCache();

    Yii::app()->controller->textBlockRegister('Сообщение', 'Сообщение успешно отправлено');
    $textBlock = TextBlock::model()->findByAttributes(array('location' => Utils::translite('Сообщение')));
    $this->assertRegExp('/сообщение успешно отправлено/iu', $textBlock->content);

    $this->clearTextBlocksCache();

    Yii::app()->controller->textBlockRegister('Регистрация', 'Успешная регистрация', array('class' => 'test_class', 'id' => 'message'));
    $textBlock = TextBlock::model()->findByAttributes(array('location' => Utils::translite('Регистрация')));
    $this->assertRegExp('/Успешная регистрация/iu', $textBlock->content);
    $this->assertRegExp('/class="test_class"/iu', $textBlock->content);
    $this->assertRegExp('/id="message"/iu', $textBlock->content);
  }

  public function testGetContacts()
  {
    $contacts = Yii::app()->controller->contacts;

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

  protected function clearTextBlocksCache()
  {
    $controller = function (FController $controller) {
      $controller->textBlocks = null;
    };

    $controller(Yii::app()->controller);
  }
} 