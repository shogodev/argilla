<?php

Yii::import('backend.tests.unit.modules.directory.common.*');
Yii::import('backend.modules.news.models.*');
Yii::import('backend.modules.news.controllers.*');
Yii::import('backend.modules.news.*');


/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @date 24.10.12
 * @package Directory
 */
class TextHtmlDirectoryTest extends DirectoryTestCase
{
  public function testGetEmptyParams()
  {
    $news = BNews::model()->findByPk(1);
    $news->attachBehavior('test', new DirectoryBehavior());
    $news->test->model = new TestDirectory();
    $news->test->field = 'section_id';
    $news->test->type  = 'text';

    $this->assertContains('input type="text"', $news->test->get());
  }

  public function testGetAutocomplete()
  {
    $this->setEnviroment();

    $news = BNews::model()->findByPk(1);
    $news->attachBehavior('test', new DirectoryBehavior());
    $news->test->model = new TestDirectory();
    $news->test->field = 'section_id';
    $news->test->type  = 'text';
    $news->test->params = array(
      'autocomplete' => true,
    );

    $this->assertContains('input type="hidden"', $news->test->get());
  }

  public function testGetAutowrite()
  {
    $this->setEnviroment();

    $news = BNews::model()->findByPk(1);
    $news->attachBehavior('test', new DirectoryBehavior());
    $news->test->model = new TestDirectory();
    $news->test->field = 'section_id';
    $news->test->type  = 'text';
    $news->test->params = array(
      'autocomplete' => true,
      'autowrite'    => true,
    );

    $creator = $news->test->getCreator();

    $this->assertContains('input type="hidden"', $news->test->get());
    $this->assertTrue(Yii::app()->getClientScript()->isScriptRegistered(BNews::model()->getFormId() . '-' . $creator->field));
  }

  protected function setEnviroment()
  {
    $controller = new BNewsController('news');
    Yii::app()->setController($controller);
  }
}