<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class RedirectedUrlCreatorTest extends CDbTestCase
{
  protected $fixtures = array(
    'seo_redirect' => 'Redirect'
  );

  public function testCreate()
  {
    $url = RedirectedUrlCreator::init('/lyzhi_gornye')->create();
    $this->assertEquals('/snoubordy', $url);
  }
}