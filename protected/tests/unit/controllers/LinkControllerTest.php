<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
/**
 * Class LinkControllerTest
 *
 * @method Link links(string $alias)
 */
class LinkControllerTest extends CDbTestCase
{
  protected $fixtures = [
    'links' => 'Link',
    'linkSections' => 'LinkSection',
  ];

  public function testActionSection()
  {
    $controller = $this->getMock('LinkController', ['render'], [], '', false);

    $controller->expects($this->once())
      ->method('render')
      ->with(
        $this->equalTo('section'),
        $this->callback(function($params)
        {
          $this->assertInstanceOf('LinkSection', $params['model']);
          $this->assertArrayHasKey('sections', $params);
          $this->assertInstanceOf('FArrayDataProvider', $params['dataProvider']);
          $this->assertInstanceOf('FFixedPageCountPagination', $params['pages']);

          return true;
        })
      );

    /** @var $controller LinkController */
    $controller->actionSection('link_section_1_url', 1);
  }
}