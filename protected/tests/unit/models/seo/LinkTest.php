<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
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
    $this->assertCount(17, $links);

    // Ожидаемый порядок: link2, link1, link4, link3, link9, link10,
    // link11, link12, link13, link14, link5, link6, link7,  link8
    $this->assertTrue($this->links('link2')->equals($links[0]));
    $this->assertTrue($this->links('link1')->equals($links[1]));
    $this->assertTrue($this->links('link4')->equals($links[2]));
    $this->assertTrue($this->links('link3')->equals($links[3]));
    $this->assertTrue($this->links('link9')->equals($links[4]));
    $this->assertTrue($this->links('link10')->equals($links[5]));
    $this->assertTrue($this->links('link11')->equals($links[6]));
    $this->assertTrue($this->links('link12')->equals($links[7]));
    $this->assertTrue($this->links('link13')->equals($links[8]));
    $this->assertTrue($this->links('link14')->equals($links[9]));

//    $this->assertTrue($this->links('link5')->equals($links[10]));
//    $this->assertTrue($this->links('link6')->equals($links[11]));
//    $this->assertTrue($this->links('link7')->equals($links[12]));
//    $this->assertTrue($this->links('link8')->equals($links[13]));
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

  public function testBeforeSaveSavesANewLinkAsInvisible()
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

  public function testBeforeSaveChoosesThePageForANewLinkCorrectly()
  {
//    // Добавим новую ссылку.
//    $link = new Link();
//    $link->setAttributes([
//      'title' => 'I am a new link',
//      'url' => 'new/link/url',
//      'section_id' => 1,
//      'email' => 'new.link@email.org',
//    ], false);
//
//    $this->assertTrue($link->isNewRecord);
//    $this->assertTrue($link->save(false));

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