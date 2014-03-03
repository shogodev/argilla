<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class FUrlManagerTest extends CTestCase
{
  /**
   * @var FUrlManager
   */
  private $manager;

  public function setUp()
  {
    $this->manager = Yii::createComponent(array(
      'class' => 'FUrlManager',
      'urlFormat' => 'path',
      'useStrictParsing' => true,
      'showScriptName' => false,
    ));

    $this->manager->init();
  }

  public function testCreateUrl()
  {
    $this->manager->addRules(array(
      'info' => array('info/index', 'pattern' => 'info/<url:\w+>'),
      'slash' => array('info/slash', 'pattern' => 'info/<url:\w+>/'),
      'article' => array('info/article', 'pattern' => 'info/<url:\w+>', 'urlSuffix' => '.html'),
    ));

    $url = $this->manager->createUrl('info/index', array('url' => 'about'), '&');
    $this->assertEquals('/info/about/', $url);

    $url = $this->manager->createUrl('info/slash', array('url' => 'about'), '&');
    $this->assertEquals('/info/about/', $url);

    $url = $this->manager->createUrl('info/article', array('url' => 'about'), '&');
    $this->assertEquals('/info/about.html', $url);

    $url = $this->manager->createUrl('info/index', array('url' => '{HTTP_HOST}/about'), '&');
    $this->assertEquals('http://unittests.dev.shogo.ru/info/about/', $url);

    $url = $this->manager->createUrl('info/index', array('url' => '{HTTP_HOST}/about/'), '&');
    $this->assertEquals('http://unittests.dev.shogo.ru/info/about/', $url);
  }
}