<?php
/**
 * @author    Vladimir Utenkov <utenkov@shogo.ru>
 * @link      https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license   http://argilla.ru/LICENSE
 */
class SitemapXml extends CComponent
{
  /**
   * @var SitemapRoute[]
   */
  private $_routes;
  /**
   * @var ILocationGenerator[]
   */
  private $_generators;

  /**
   * @param SitemapRoute[]       $routes
   * @param ILocationGenerator[] $generators
   */
  public function __construct(array $routes, array $generators)
  {
    $this->_routes     = $routes;
    $this->_generators = $generators;
  }

  /**
   * @param SitemapUrlBuilder $urlBuilder
   * @param DateTime          $currentDate
   *
   * @return string
   */
  public function build(SitemapUrlBuilder $urlBuilder, DateTime $currentDate)
  {
    $urlsetElement = new SimpleXMLElement(
      '<?xml version="1.0" encoding="utf-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>'
    );

    $xclusions = new SitemapExclusion();
    $xclusions->setFullExclusion();

    foreach( $this->_routes as $route )
    {
      if( ($locations = $this->getLocationGeneratorForRoute($route)) !== null )
      {
        /** @var $location string */
        foreach( $locations as $location )
        {
          $lastmod    = $route->lastmod;
          $changefreq = $route->changefreq;
          $priority   = $route->priority;

          /** Исключения для URL */
          if( ($exclusion = $xclusions->getExclusion($location)) !== null )
          {
            $lastmod    = $exclusion->lastmod;
            $changefreq = $exclusion->changefreq;
            $priority   = $exclusion->priority;
          }

          $urlElement = $this->createUrlElement($urlsetElement);
          $urlBuilder->addLoc($urlElement, $location);
          if( !empty($lastmod) )
          {
            $urlBuilder->addLastMod($urlElement, $currentDate);
          }
          $urlBuilder->addChangeFreq($urlElement, $changefreq);
          $urlBuilder->addPriority($urlElement, $priority);
        }
      }
    }

    $otherLocations = $xclusions->getOtherExclusion();

    /** @var $location string */
    foreach( $otherLocations as $location )
    {
      $urlElement = $this->createUrlElement($urlsetElement);
      $urlBuilder->addLoc($urlElement, $location->route);
      if( !empty($location->lastmod) )
      {
        $urlBuilder->addLastMod($urlElement, $currentDate);
      }
      $urlBuilder->addChangeFreq($urlElement, $location->changefreq);
      $urlBuilder->addPriority($urlElement, $location->priority);
    }

    return $urlsetElement->asXML();
  }

  /**
   * @param SitemapRoute $routes
   *
   * @return ILocationGenerator|null
   */
  private function getLocationGeneratorForRoute(SitemapRoute $routes)
  {
    foreach( $this->_generators as $generator )
    {
      if( $generator->getRoute() === $routes->route )
      {
        return $generator;
      }
    }

    return null;
  }

  /**
   * @param SimpleXMLElement $urlsetElement
   *
   * @return SimpleXMLElement
   */
  private function createUrlElement(SimpleXMLElement $urlsetElement)
  {
    return $urlsetElement->addChild('url');
  }
}