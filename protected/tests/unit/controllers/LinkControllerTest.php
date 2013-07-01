<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
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
        $this->logicalAnd(
          $this->arrayHasKey('model'),
          $this->arrayHasKey('sections'),
          $this->arrayHasKey('dataProvider'),
          $this->arrayHasKey('pages')
        )
      );

    /** @var $controller LinkController */
    $controller->actionSection('link_section_1_url', 1);
  }
}