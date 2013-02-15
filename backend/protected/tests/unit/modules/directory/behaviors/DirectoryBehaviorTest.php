<?php

Yii::import('backend.tests.unit.modules.directory.common.*');
Yii::import('backend.modules.news.models.*');

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date 24.10.12
 * @package Directory
 */
class DirectoryBehaviorTest extends DirectoryTestCase
{
  public function testInit()
  {
    $news = BNews::model()->findByPk(1);
    $news->attachBehavior('test', new DirectoryBehavior());

    $news->test->model = new TestDirectory();
    $news->test->field = 'section_id';
    $news->test->type  = 'select';

    $this->assertNotEmpty($news->test->init());
  }

  public function testInitFail()
  {
    $this->setExpectedException('DirectoryException');
    
    $news = BNews::model()->findByPk(1);
    $news->attachBehavior('test', new DirectoryBehavior());
    $news->test->init();
  }

  public function testGet()
  {
    $news = BNews::model()->findByPk(1);
    $news->attachBehavior('test', new DirectoryBehavior());

    $news->test->model = new TestDirectory();
    $news->test->field = 'section_id';
    $news->test->type  = 'select';
    $news->test->init();

    $this->assertNotEmpty($news->test->get());
  }

  public function testGetCreator()
  {
    $news = BNews::model()->findByPk(1);
    $news->attachBehavior('test', new DirectoryBehavior());

    $news->test->model = new TestDirectory();
    $news->test->field = 'section_id';
    $news->test->type  = 'select';
    $news->test->init();

    $this->assertInstanceOf('AbstractDirectoryHtmlCreator', $news->test->getCreator());
  }

  public function testGetWithStringModel()
  {
    $news = BNews::model()->findByPk(1);
    $news->attachBehavior('test', new DirectoryBehavior());

    $news->test->model = 'TestDirectory';
    $news->test->field = 'section_id';
    $news->test->type  = 'select';
    $news->test->init();

    $this->assertInstanceOf('CActiveRecord', $news->test->model);
  }
}