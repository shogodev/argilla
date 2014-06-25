<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.controllers.behaviors
 */
class TextBlockBehaviorTest extends CDbTestCase
{
  protected $fixtures = array(
    'text_block' => 'TextBlock',
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
    $this->assertEquals($textBlock, 'Текст 2');

    $textBlock = Yii::app()->controller->textBlock('mainNotVisible');
    $this->assertEmpty($textBlock);

    $textBlock = Yii::app()->controller->textBlock('doesNotExist');
    $this->assertEmpty($textBlock);
  }

  public function testTextBlocks()
  {
    $textBlocks = Yii::app()->controller->textBlocks('main');
    $this->assertCount(2, $textBlocks);
    $this->assertEquals($textBlocks[2], 'Текст 2');
    $this->assertEquals($textBlocks[1], 'Текст 1');

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

    $this->clearTextBlocksCache();

    Yii::app()->controller->textBlockRegister('test_registration_not_visible', 'Успешная регистрация');
    $textBlock = TextBlock::model()->resetScope()->findAllByAttributes(array('location' => 'test_registration_not_visible'));
    $this->assertTrue(count($textBlock) == 1);
  }

  public function testReplace()
  {
    $model = TextBlock::model();
    $text = Arr::reset($model->getByLocation('testReplace'));
    $this->assertEquals($text->content, 'text {before}');
    $textBlock = Yii::app()->controller->textBlock('testReplace', array('{before}' => 'after'));
    $this->assertEquals($textBlock, 'text after');

    $textBlock = Yii::app()->controller->textBlockRegister('test_replace_registration', null, null, array('{before}' => 'after register text block'));
    $this->assertEquals($textBlock, 'text3 after register text block');
  }

  public function testReplaceByTextBlock()
  {
    $model = TextBlock::model();
    $text = Arr::reset($model->getByLocation('testReplaceByTextBlock'));
    $this->assertEquals($text->content, 'text2 {replaced_text_block}');
    $textBlock = Yii::app()->controller->textBlock('testReplaceByTextBlock');
    $this->assertEquals($textBlock, 'text2 new replaced text');
  }

  protected function clearTextBlocksCache()
  {
    $controller = function (FController $controller) {
      $controller->textBlocks = null;
    };

    $controller(Yii::app()->controller);
  }
}