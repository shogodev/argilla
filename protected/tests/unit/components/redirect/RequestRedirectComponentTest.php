<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class RequestRedirectComponentTest extends CDbTestCase
{
  protected $fixtures = array(
    'seo_redirect' => 'Redirect'
  );

  public function testDoNotMakeSlashRedirect()
  {
    $component = new RequestRedirectComponent(null, false);

    $component->setRequest('testUrl/');
    $component->init();
    $component->makeSlashRedirect();
    $this->assertNotNull($component);

    $component->setRequest('testUrl.html');
    $component->init();
    $component->makeSlashRedirect();
    $this->assertNotNull($component);
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage Location: testUrl/
   */
  public function testMakeSlashRedirect()
  {
    $component = new RequestRedirectComponent('testUrl', false);
    $component->init();
    $component->makeSlashRedirect();
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage Location: testUrl/?param1=value1
   */
  public function testMakeSlashRedirectWithGet()
  {
    $component = new RequestRedirectComponent('testUrl?param1=value1', false);
    $component->init();
    $component->makeSlashRedirect();
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage Location: /
   */
  public function testMakeIndexRedirect1()
  {
    $component = new RequestRedirectComponent('/index.php', false);
    $component->init();
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage Location: /
   */
  public function testMakeIndexRedirect2()
  {
    $component = new RequestRedirectComponent('/index.php/', false);
    $component->init();
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage Location: /testUrl/
   */
  public function testMakeIndexRedirect()
  {
    $component = new RequestRedirectComponent('/index.php/testUrl/', false);
    $component->init();
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage Location: /elki_palki/
   * @expectedExceptionCode 301
   */
  public function testFindByKey()
  {
    $component = new RequestRedirectComponent('/palki/', false);
    $component->init();
    $component->processRequest();
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage Location: /
   * @expectedExceptionCode 301
   */
  public function testIndexRedirect()
  {
    $component = new RequestRedirectComponent('/argilla/', false);
    $component->init();
    $component->processRequest();
  }

  public function testFindByPattern()
  {
    $component = new RequestRedirectComponent('/lyzhnoe_snaryazhenie/2/', false);
    $component->init();
    $component->processRequest();

    $this->assertEquals('/aksessuary_dlya_lyzh/2/', $_SERVER['REQUEST_URI']);
  }

  /**
   * @expectedException CHttpException
   * @expectedExceptionMessage 404 - Not found
   */
  public function testOriginExists()
  {
    $component = new RequestRedirectComponent('/begovie/', false);
    $component->init();
    $component->processRequest();
  }

  /**
   * @expectedException CHttpException
   * @expectedExceptionMessage 404 - Not found
   */
  public function testFind404()
  {
    $component = new RequestRedirectComponent('/figurnye/', false);
    $component->init();
    $component->processRequest();
  }
}