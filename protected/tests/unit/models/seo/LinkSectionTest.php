<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
/**
 * Class LinkSectionTest
 *
 * @method LinkSection linkSections(string $alias)
 * @method Link links(string $alias)
 */
class LinkSectionTest extends CDbTestCase
{
  protected $fixtures = [
    'linkSections' => 'LinkSection',
    'links' => 'Link'
  ];

  public function testDefaultScope()
  {
    $sections = LinkSection::model()->findAll();

    $this->assertNotEmpty($sections);
    $this->assertCount(5, $sections);

    // Ожидаемый порядок: linkSection2, linkSection3, linkSection1, linkSection6, linkSection7
    $this->assertTrue($this->linkSections('linkSection2')->equals($sections[0]));
    $this->assertTrue($this->linkSections('linkSection3')->equals($sections[1]));
    $this->assertTrue($this->linkSections('linkSection1')->equals($sections[2]));
    $this->assertTrue($this->linkSections('linkSection6')->equals($sections[3]));
    $this->assertTrue($this->linkSections('linkSection7')->equals($sections[4]));
  }


  public function testLinksRelation()
  {
    /** @var $section LinkSection */
    $section = LinkSection::model()->findByPk(1);
    $links = $section->links;

    $this->assertNotEmpty($links);
    $this->assertCount(4, $links);
  }

  public function testLinksRelationWhenThereAreNoLinksInSection()
  {
    /** @var $section LinkSection */
    $section = LinkSection::model()->findByPk(7);
    $links = $section->links;

    $this->assertNotNull($links);
    $this->assertEmpty($links);
  }

  public function testLinkCountRelation()
  {
    /** @var $section LinkSection */
    $section = LinkSection::model()->findByPk(1);

    $this->assertEquals(4, $section->linkCount);
  }

  public function testLinkCountRelationWhenThereAreNoLinksinSection()
  {
    /** @var $section LinkSection */
    $section = LinkSection::model()->findByPk(7);

    $this->assertEquals(0, $section->linkCount);
  }


  public function testWhereUrl()
  {
    $section = LinkSection::model()->whereUrl('link_section_6_url')->find();

    $this->assertNotNull($section);
    $this->assertTrue($this->linkSections('linkSection6')->equals($section));
  }

  public function testWhereUrlWhenThereIsNoSectionWithUrlSpecified()
  {
    $section = LinkSection::model()->whereUrl('not_existing_link_section')->find();

    $this->assertNull($section);
  }


  public function testGetPageCount()
  {
    /** @var $section LinkSection */
    $section = LinkSection::model()->findByPk(3);

    $this->assertEquals(3, $section->getPageCount());
  }

  public function testGetPageCountWhenThereNoPages()
  {
    /** @var $section LinkSection */
    $section = LinkSection::model()->findByPk(7);

    $this->assertEquals(0, $section->getPageCount());
  }

  public function testGetLinksOnPage()
  {
    /** @var $section LinkSection */
    $section = LinkSection::model()->findByPk(1);

    $links = $section->getLinksOnPage(1);

    $this->assertNotEmpty($links);
    $this->assertContainsOnly('Link', $links);
    $this->assertCount(4, $links);

    // Ожидаемые ссылки: link2, link1, link4, link3
    $this->assertTrue($this->links('link2')->equals($links[0]));
    $this->assertTrue($this->links('link1')->equals($links[1]));
    $this->assertTrue($this->links('link4')->equals($links[2]));
    $this->assertTrue($this->links('link3')->equals($links[3]));
  }

  public function testGetLinksOnPageWhenThereAreNoLinks()
  {
    /** @var $section LinkSection */
    $section = LinkSection::model()->findByPk(1);

    $links = $section->getLinksOnPage(42);

    $this->assertEmpty($links);
  }
}