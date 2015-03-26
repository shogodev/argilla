<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.controllers.behaviors
 */
class SeoBehaviorTest extends CDbTestCase
{
  protected $fixtures = array(
    'seo_counters' => 'Counter',
    'seo_link_block' => 'LinkBlock',
    'info' => 'Info',
  );

  public function setUp()
  {
    Yii::app()->setUnitEnvironment('Index', 'index');

    parent::setUp();
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
}