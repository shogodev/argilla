<?php
/**
 * @author    Vladimir Utenkov <utenkov@shogo.ru>
 * @link      https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license   http://argilla.ru/LICENSE
 */
class SitemapUrlBuilderTest extends CTestCase
{
  public function testAddLoc()
  {
    $url = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><url></url>');

    $builder = new SitemapUrlBuilder();
    $builder->addLoc($url, 'pisch pisch');

    $expected = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><url><loc>pisch pisch</loc></url>');
    $this->assertXmlStringEqualsXmlString($expected->asXML(), $url->asXML());
  }

  public function testAddLastMod()
  {
    $url = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><url></url>');

    $builder = new SitemapUrlBuilder();
    $builder->addLastMod($url, new DateTime('15-07-2013'));

    $expected = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><url><lastmod>2013-07-15</lastmod></url>');
    $this->assertXmlStringEqualsXmlString($expected->asXML(), $url->asXML());
  }

  public function testAddChangeFreq()
  {
    $url = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><url></url>');

    $builder = new SitemapUrlBuilder();
    $builder->addChangeFreq($url, 'monthly');

    $expected = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><url><changefreq>monthly</changefreq></url>');
    $this->assertXmlStringEqualsXmlString($expected->asXML(), $url->asXML());
  }

  public function testAddPriority()
  {
    $url = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><url></url>');

    $builder = new SitemapUrlBuilder();
    $builder->addPriority($url, 0.9);

    $expected = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><url><priority>0.9</priority></url>');
    $this->assertXmlStringEqualsXmlString($expected->asXML(), $url->asXML());
  }
}