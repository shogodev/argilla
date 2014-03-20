<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ReplaceRedirectComponentTest extends CDbTestCase
{
  protected $fixtures = array(
    'seo_redirect' => 'Redirect'
  );

  /**
   * @var ReplaceRedirectComponent
   */
  private $creator;

  protected function setUp()
  {
    parent::setUp();
    $this->creator = Yii::createComponent('ReplaceRedirectComponent');
    $this->creator->init();
  }

  public function testGetUrlByKey()
  {
    $url = $this->creator->getUrl('/lyzhi_gornye/');
    $this->assertEquals('/snoubordy/', $url);
  }

  public function testGetUrlByPattern()
  {
    $url = $this->creator->getUrl('/komplekty/');
    $this->assertEquals('/aksessuary_dlya_lyzh/', $url);

    $url = $this->creator->getUrl('/komplekty/4/');
    $this->assertEquals('/aksessuary_dlya_lyzh/4/', $url);

    $url = $this->creator->getUrl('/snoubordy/4/?param=value');
    $this->assertEquals('/aksessuary_dlya_lyzh/4/?param=value', $url);
  }
}