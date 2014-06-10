<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.sitemapXml.locationCenerators
 */
class SitemapUrlBuilder extends CComponent
{
  /**
   * @param SimpleXMLElement $urlElement
   * @param string           $loc
   */
  public function addLoc(SimpleXMLElement $urlElement, $loc)
  {
    $urlElement->addChild('loc', XmlHelper::escape($loc));
  }

  /**
   * @param SimpleXMLElement $urlElement
   * @param DateTime         $date
   */
  public function addLastMod(SimpleXMLElement $urlElement, DateTime $date)
  {
    $days    = 1;
    $day     = 24 * 60 * 60;
    $add     = ((rand(9, 19)) * 60 + rand(1, 59)) * 60 + rand(1, 59);
    $time    = (floor(time() / $day) - $days) * $day + $add;
    $lastmod = substr_replace(date('Y-m-d\TH:iO', $time), ':', -2).substr(date('O', $time), 3);

    $urlElement->addChild('lastmod', $lastmod);
  }

  /**
   * @param SimpleXMLElement $urlElement
   * @param string           $changeFrequency
   */
  public function addChangeFreq(SimpleXMLElement $urlElement, $changeFrequency)
  {
    $urlElement->addChild('changefreq', $changeFrequency);
  }

  /**
   * @param SimpleXMLElement $urlElement
   * @param float            $priority
   */
  public function addPriority(SimpleXMLElement $urlElement, $priority)
  {
    $urlElement->addChild('priority', strval($priority));
  }
}