<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.sitemap
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
    $urlElement->addChild('lastmod', $date->format('Y-m-d'));
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