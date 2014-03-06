<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class RedirectHelperTest extends CDbTestCase
{
  protected $fixtures = array(
    'seo_redirect' => 'Redirect'
  );

  public function testDoNotMakeSlashRedirect()
  {
    $helper = new RedirectHelper();

    $helper->setCurrentUrl('testUrl/');
    $helper->init();
    $this->assertNotNull($helper);

    $helper->setCurrentUrl('testUrl.html');
    $helper->init();
    $this->assertNotNull($helper);
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage Location: testUrl/
   */
  public function testMakeSlashRedirect()
  {
    $helper = new RedirectHelper('testUrl');
    $helper->init();
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionMessage testUrl/?param1=value1
   */
  public function testMakeSlashRedirectWithGet()
  {
    $helper = new RedirectHelper('testUrl?param1=value1');
    $helper->init();
  }

  public function testFind()
  {
    $helper = new RedirectHelper();

    $helper->setCurrentUrl('/palki/');
    $helper->find();
    $this->assertTrue($helper->isRedirect);
    $this->assertEquals('/elki_palki', $helper->targetUrl);
    $this->assertEquals(RedirectType::TYPE_301, $helper->getRedirect()->type_id);

    $helper->setCurrentUrl('/komplekty/2/');
    $helper->find();
    $this->assertTrue($helper->isRedirect);
    $this->assertEquals('/aksessuary_dlya_lyzh/2', $helper->targetUrl);
    $this->assertEquals(RedirectType::TYPE_301, $helper->getRedirect()->type_id);

    $helper->setCurrentUrl('/motalki/');
    $helper->find();
    $this->assertFalse($helper->isRedirect);
    $this->assertNull($helper->getRedirect());
    $this->assertNull($helper->targetUrl);

    $helper->setCurrentUrl('/snoubordy/');
    $helper->find();
    $this->assertTrue($helper->isRedirect);
    $this->assertEquals('/lyzhi_gornye', $helper->targetUrl);
    $this->assertEquals(RedirectType::TYPE_REPLACE, $helper->getRedirect()->type_id);
  }

  /**
   * @expectedException CHttpException
   * @expectedExceptionMessage 404 - Not found
   */
  public function testOriginExists()
  {
    $helper = new RedirectHelper('/lyzhi_begovie/');
    $helper->init();
    $helper->find()->move();
  }

  /**
   * @expectedException CHttpException
   * @expectedExceptionMessage 404 - Not found
   */
  public function testMove404()
  {
    $helper = new RedirectHelper('/figurnye');
    $helper->find()->move();
  }

  /**
   * @expectedException TRedirectException
   * @expectedExceptionCode 301
   */
  public function testMove301()
  {
    $helper = new RedirectHelper('/palki');
    $helper->find()->move();
  }

  public function testMoveReplace()
  {
    $helper = new RedirectHelper('/snoubordy');
    $helper->find()->move();
    $this->assertEquals('/lyzhi_gornye', $_SERVER['REQUEST_URI']);
  }
}