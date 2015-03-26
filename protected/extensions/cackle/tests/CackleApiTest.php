<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
Yii::import('ext.cackle.*');

class CackleApiTest extends CTestCase
{
  public function testGetComments()
  {
    $cackleApi = new CackleApi();
    $reviews = $cackleApi->getReviews();

    $this->assertTrue(false);
  }
}