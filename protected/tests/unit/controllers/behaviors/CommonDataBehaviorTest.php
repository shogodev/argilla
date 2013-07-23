<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.controllers.behaviors
 */
class CommonDataBehaviorTest extends CDbTestCase
{
  protected $fixtures = array(
    'contact_field' => 'ContactField',
    'contact_group' => 'ContactGroup',
    'text_block' => 'TextBlock',
    'seo_counters' => 'Counter',
    'seo_link_block' => 'LinkBlock',
    'info' => 'Info',
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
    $textBlock = TextBlock::model()->findByAttributes(array('location' => 'index/'.Utils::translite('Сообщение')));
    $this->assertRegExp('/сообщение успешно отправлено/iu', $textBlock->content);

    $this->clearTextBlocksCache();

    Yii::app()->controller->textBlockRegister('Регистрация', 'Успешная регистрация', array('class' => 'test_class', 'id' => 'message'));
    $textBlock = TextBlock::model()->findByAttributes(array('location' => 'index/'.Utils::translite('Регистрация')));
    $this->assertRegExp('/Успешная регистрация/iu', $textBlock->content);
    $this->assertRegExp('/class="test_class"/iu', $textBlock->content);
    $this->assertRegExp('/id="message"/iu', $textBlock->content);
  }

  public function testGetCounters()
  {
    // выбирется все кроме флага на главной
    Yii::app()->setUnitEnvironment('Info', 'index', array('url' => 'o_kompanii'));

    $counters = Yii::app()->controller->getCounters();

    $this->assertCount(2, $counters);
    $this->assertContains('Код счетчика rambler', $counters);
    $this->assertContains('Код счетчика google', $counters);
    $this->assertNotContains('Код счетчика yandex', $counters);

    $this->assertEmpty(array_diff($counters, Yii::app()->controller->counters));

    // выбирается все
    Yii::app()->setUnitEnvironment('Index', 'index');

    $counters = Yii::app()->controller->getCounters();
    $this->assertCount(4, $counters);
    $this->assertContains('Код счетчика rambler', $counters);
    $this->assertContains('Код счетчика google', $counters);
    $this->assertNotContains('Код счетчика yandex', $counters);
    $this->assertContains('Код счетчика google на главной', $counters);
    $this->assertContains('Код счетчика yandex на главной', $counters);

    $this->assertEmpty(array_diff($counters, Yii::app()->controller->counters));
  }

  public function testGetCopyrights()
  {
    Yii::app()->request->setRequestUri('/');

    $copyrights = Yii::app()->controller->copyrights;
    $this->assertCount(3, $copyrights);

    $this->assertContains('Код 4', $copyrights);
    $this->assertContains('Код 2', $copyrights);
    $this->assertContains('Код 1 '.date("Y"), $copyrights);

    $copyrights = Yii::app()->controller->getCopyrights('socials');
    $this->assertCount(2, $copyrights);
    $this->assertContains('Код 7', $copyrights);
    $this->assertContains('Код 8', $copyrights);

    $copyrights = Yii::app()->controller->getCopyrights('doesNotExistsKey');
    $this->assertEmpty($copyrights);

    Yii::app()->request->setRequestUri('url/');

    $copyrights = Yii::app()->controller->copyrights;
    $this->assertCount(2, $copyrights);

    $copyrights = Yii::app()->controller->getCopyrights('socials');
    $this->assertCount(2, $copyrights);

    $this->assertContains('Код 5', $copyrights);
    $this->assertContains('Код 7', $copyrights);

    Yii::app()->request->setRequestUri('/');

    $copyrights = Yii::app()->controller->getCopyrights('new');
    $this->assertCount(1, $copyrights);
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

  protected function clearTextBlocksCache()
  {
    $controller = function (FController $controller) {
      $controller->textBlocks = null;
    };

    $controller(Yii::app()->controller);
  }
}