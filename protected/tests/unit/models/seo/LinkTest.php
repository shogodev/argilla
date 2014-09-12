<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
/**
 * Class LinkTest
 *
 * @method LinkSection linkSections(string $alias)
 * @method Link links(string $alias)
 */
class LinkTest extends CDbTestCase
{
  protected $fixtures = [
    'linkSections' => 'LinkSection',
    'links' => 'Link',
  ];

  public function testDefaultScope()
  {
    $links = Link::model()->findAll();

    $this->assertNotEmpty($links);
    $this->assertContainsOnly('Link', $links);

    $this->assertTrue($this->links('link2')->equals($links[0]));
  }

  public function testOnPage()
  {
    $links = Link::model()->visible()->onPage(2)->findAll();

    $this->assertNotEmpty($links);
    $this->assertContainsOnly('Link', $links);
    $this->assertCount(3, $links);

    // Ожидаемые ссылки: link9, link10, link11
    $this->assertTrue($this->links('link9')->equals($links[0]));
    $this->assertTrue($this->links('link10')->equals($links[1]));
    $this->assertTrue($this->links('link11')->equals($links[2]));
  }

  public function testOnPageWhenThereAreNoLinksOnThePageSpecified()
  {
    $links = Link::model()->visible()->onPage(42)->findAll();

    $this->assertEmpty($links);
  }

  public function testInSection()
  {
    $links = Link::model()->visible()->inSection(1)->findAll();

    $this->assertNotEmpty($links);
    $this->assertContainsOnly('Link', $links);
    $this->assertCount(4, $links);
  }

  public function testInSectionWhenThereAreNoLinksInTheSectionSpecified()
  {
    $links = Link::model()->visible()->inSection(42)->findAll();

    $this->assertEmpty($links);
  }

  public function testLinksOnPagesBefore()
  {
    $links = Link::model()->visible()->linksOnPagesBefore(3)->findAll();

    $this->assertNotEmpty($links);
    $this->assertContainsOnly('Link', $links);
    $this->assertCount(7, $links);
  }

  public function testLinksOnPagesBeforeWhenThereAreNoLinksBeforeThePageSpecified()
  {
    $links = Link::model()->visible()->linksOnPagesBefore(0)->findAll();

    $this->assertEmpty($links);
  }

  public function testBeforeSaveSavesNewLinkAsInvisible()
  {
    // Добавим новую ссылку.
    $link = new Link();
    $link->setAttributes([
      'title' => 'I am a new link',
      'url' => 'new/link/url',
      'section_id' => 1,
      'email' => 'link@email.org',
    ], false);

    $this->assertTrue($link->isNewRecord);
    $this->assertTrue($link->save(false));
    $this->assertFalse($link->isNewRecord);

    // Проверяем, что ссылка невидимая.
    /** @var $link Link */
    $link = Link::model()->findByPk($link->primaryKey);

    $this->assertNotNull($link);
    $this->assertInstanceOf('Link', $link);
    $this->assertEquals('0', $link->visible);
  }

  public function testBeforeSaveChoosesThePageForNewLinkCorrectly()
  {
    // Добавим новую ссылку.
    $link = new Link();
    $link->setAttributes([
      'title' => 'I am a new link',
      'url' => 'new/link/url',
      'section_id' => 1,
      'email' => 'new.link@email.org',
    ], false);

    $this->assertTrue($link->isNewRecord);
    $this->assertTrue($link->insert());
    $this->assertFalse($link->isNewRecord);

    // Ссылка должна добавится на страницу 2,
    // так как на странице 1 кол-во ссылок >= Link::LINKS_PER_PAGE,
    // а на странице 2 кол-во ссылок < Link::LINKS_PER_PAGE.
    /** @var $link Link */
    $link = Link::model()->findByPk($link->primaryKey);

    $this->assertNotNull($link);
    $this->assertEquals('2', $link->page);
  }
}