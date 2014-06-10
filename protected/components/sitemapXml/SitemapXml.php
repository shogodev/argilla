<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.sitemapXml.locationCenerators
 */
class SitemapXml extends CComponent
{
  private $charset = 'utf-8';

  /**
   * @var SitemapRoute[]
   */
  private $_routes;

  /**
   * @var ILocationGenerator[]
   */
  private $_generators;

  /**
   * @var SimpleXMLElement
   */
  private $urlsetElement;

  /**
   * @param SitemapRoute[]       $routes
   * @param ILocationGenerator[] $generators
   */
  public function __construct(array $routes, array $generators)
  {
    $this->_routes     = $routes;
    $this->_generators = $generators;
  }

  public function init()
  {
    /**
     * Empty method for XmlExportController
     */
  }

  /**
   * Renders sitemap xml
   *
   * @return void
   */
  public function render()
  {
    header('Content-Type: text/xml; charset='.$this->charset);
    echo $this->urlsetElement->asXML();
    Yii::app()->end();
  }

  /**
   * @param SitemapUrlBuilder $urlBuilder
   * @param DateTime          $currentDate
   *
   * @return string
   */
  public function build(SitemapUrlBuilder $urlBuilder, DateTime $currentDate)
  {
    $this->urlsetElement = new SimpleXMLElement(
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

          $urlElement = $this->createUrlElement($this->urlsetElement);
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
      $urlElement = $this->createUrlElement($this->urlsetElement);
      $urlBuilder->addLoc($urlElement, $location->route);
      if( !empty($location->lastmod) )
      {
        $urlBuilder->addLastMod($urlElement, $currentDate);
      }
      $urlBuilder->addChangeFreq($urlElement, $location->changefreq);
      $urlBuilder->addPriority($urlElement, $location->priority);
    }
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