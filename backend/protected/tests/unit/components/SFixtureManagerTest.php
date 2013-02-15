<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 27.08.12
 */
class SFixtureManagerTest extends CTestCase
{
  public function testLoadFixture()
  {
    $prefix      = Yii::app()->getDb()->tablePrefix;
    $transaction = Yii::app()->db->beginTransaction();
    $manager     = new BFixtureManager();

    $manager->init();
    $manager->truncateTable($prefix.'news');
    $manager->truncateTable($prefix.'news_section');
    $fixtures = $manager->loadFixture($prefix.'news_section');

    $this->assertArrayHasKey('section1', $fixtures);
    $this->assertArrayHasKey('section2', $fixtures);

    $transaction->rollback();
  }
}

?>