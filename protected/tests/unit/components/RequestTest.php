<?php
/**
 * User: glagolev
 * Date: 20.08.12
 */
class RequestTest extends CTestCase
{
  public function testCutQueryParams()
  {
    $query = 'backend/news/news/index?News_page=3&News_sort=title&ajax=yw0';
    $query = Request::cutQueryParams($query, array('ajax'));

    $this->assertNotRegExp("/ajax=\w/", $query);
    $this->assertRegExp("/News_sort=\w/", $query);

    $query = 'backend/news/news/index';
    $cut   = Request::cutQueryParams($query, array('ajax'));
    $this->assertEquals($query, $cut);
  }
}

?>