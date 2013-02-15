<?php

Yii::import('backend.tests.unit.modules.directory.common.*');
Yii::import('backend.modules.news.models.*');

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date 24.10.12
 * @package Directory
 */
class RadioDirectoryHtmlCreatorTest extends DirectoryTestCase
{
  public function testGet()
  {
    $news = BNews::model()->findByPk(1);
    $news->attachBehavior('test', new DirectoryBehavior());
    $news->test->model = new TestDirectory();
    $news->test->field = 'section_id';
    $news->test->type  = 'radio';

    $this->assertContains('type="radio"', $news->test->get());
  }
}