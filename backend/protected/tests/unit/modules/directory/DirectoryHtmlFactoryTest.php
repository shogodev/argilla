<?php

Yii::import('backend.tests.unit.modules.directory.common.*');
Yii::import('backend.modules.news.models.*');

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date 24.10.12
 * @package Directory
 */
class DirectoryHtmlFactoryTest extends DirectoryTestCase
{
  public function testCreatorSelectClass()
  {
    $news = BNews::model()->findByPk(1);
    $news->attachBehavior('test', new DirectoryBehavior());
    $news->test->model = new TestDirectory();
    $news->test->field = 'section_id';
    $news->test->type  = 'select';
    $news->test->init();

    $this->assertInstanceOf('SelectDirectoryHtmlCreator', $news->test->getCreator());
  }

  public function testCreatorTextClass()
  {
    $news = BNews::model()->findByPk(1);
    $news->attachBehavior('test', new DirectoryBehavior());
    $news->test->model = new TestDirectory();
    $news->test->field = 'section_id';
    $news->test->type  = 'text';
    $news->test->init();

    $this->assertInstanceOf('TextDirectoryHtmlCreator', $news->test->getCreator());
  }

  public function testCreatorRadioClass()
  {
    $news = BNews::model()->findByPk(1);
    $news->attachBehavior('test', new DirectoryBehavior());
    $news->test->model = new TestDirectory();
    $news->test->field = 'section_id';
    $news->test->type  = 'radio';
    $news->test->init();

    $this->assertInstanceOf('RadioDirectoryHtmlCreator', $news->test->getCreator());
  }

  public function testCreatorCheckClass()
  {
    $news = BNews::model()->findByPk(1);
    $news->attachBehavior('test', new DirectoryBehavior());
    $news->test->model = new TestDirectory();
    $news->test->field = 'section_id';
    $news->test->type  = 'checkbox';
    $news->test->init();

    $this->assertInstanceOf('CheckDirectoryHtmlCreator', $news->test->getCreator());
  }

  public function testInitCreatorClassFail()
  {
    $this->setExpectedException('DirectoryException');

    $news = BNews::model()->findByPk(1);
    $news->attachBehavior('test', new DirectoryBehavior());
    $news->test->model = new TestDirectory();
    $news->test->field = 'section_id';
    $news->test->type  = uniqid();
    $news->test->init();
  }
}