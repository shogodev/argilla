<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
Yii::import('ext.cackle.models.*');

class CackleTest extends CTestCase
{
  public function setUp()
  {
    CackleReview::model()->deleteAll();
  }

  public function testCreate()
  {
    $cackle = new CackleSync(new CackleReviewManager());
    $cackle->create();

    $cackle = new CackleSync(new CackleCommentManager());
    $cackle->update();
  }
} 