<?php
/**
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.sitemap
 */

Yii::import('frontend.models.xml.AbstractXml');

/**
 * Class SitemapXml
 */
class SitemapXml extends AbstractXml
{
  public $charset = 'utf-8';

  /**
   * @var SitemapRoute[]
   */
  private $routes;

  /**
   * @var ILocationGenerator[]
   */
  private $generators;

  /**
   * @var SitemapUrlBuilder
   */
  private $builder;

  /**
   * @var DateTime
   */
  private $currentDate;

  /**
   * @param ILocationGenerator[] $generators
   */
  public function setGenerators(array $generators)
  {
    $this->generators = $generators;
  }

  /**
   * @param SitemapRoute[] $routes
   */
  public function setRoutes(array $routes)
  {
    $this->routes = $routes;
  }

  public function init()
  {
    if( !isset($this->builder) )
      $this->builder = new SitemapUrlBuilder();

    if( !isset($this->currentDate) )
      $this->currentDate = new DateTime();

    parent::init();
  }

  public function buildXml()
  {
    $xclusions = new SitemapExclusion();
    $xclusions->setFullExclusion();

    foreach($this->routes as $route)
    {
      if( ($locations = $this->getLocationGeneratorForRoute($route)) !== null )
      {
        foreach($locations as $location)
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

          $urlElement = $this->createUrlElement($this->xmlDocument);
          $this->builder->addLoc($urlElement, $location);
          if( !empty($lastmod) )
          {
            $this->builder->addLastMod($urlElement, $this->currentDate);
          }
          $this->builder->addChangeFreq($urlElement, $changefreq);
          $this->builder->addPriority($urlElement, $priority);
        }
      }
    }

    $otherLocations = $xclusions->getOtherExclusion();

    foreach($otherLocations as $location)
    {
      $urlElement = $this->createUrlElement($this->xmlDocument);
      $this->builder->addLoc($urlElement, $location->route);
      if( !empty($location->lastmod) )
      {
        $this->builder->addLastMod($urlElement, $this->currentDate);
      }
      $this->builder->addChangeFreq($urlElement, $location->changefreq);
      $this->builder->addPriority($urlElement, $location->priority);
    }
  }

  /**
   * @param SitemapRoute $routes
   *
   * @return ILocationGenerator|null
   */
  private function getLocationGeneratorForRoute(SitemapRoute $routes)
  {
    foreach( $this->generators as $generator )
    {
      if( $generator->getRoute() === $routes->route )
      {
        return $generator;
      }
    }

    return null;
  }

  /**
   * @param SimpleXMLElement $xmlDocument
   *
   * @return SimpleXMLElement
   */
  private function createUrlElement(SimpleXMLElement $xmlDocument)
  {
    return $xmlDocument->addChild('url');
  }
}