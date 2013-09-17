<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.redirect
 */
class RedirectedUrlCreator extends CComponent
{
  const CACHE_PREFIX = 'redirect_url_creator::';

  /**
   * @var string
   */
  private $baseUrl;

  /**
   * @var string
   */
  private $target;

  /**
   * @var CCache
   */
  private $cache;

  /**
   * @var RedirectHelper
   */
  private $seoRedirect;

  /**
   * @param string $baseUrl
   * @param CCache $cache
   * @param RedirectHelper $seoRedirect
   */
  private function __construct($baseUrl, $cache, $seoRedirect)
  {
    $this->baseUrl = $baseUrl;
    $this->cache = isset($cache) ? $cache : Yii::app()->cache;
    $this->seoRedirect = isset($seoRedirect) ? $seoRedirect : Yii::app()->seoRedirect;
  }

  /**
   * @param string $baseUrl
   *
   * @param CCache $cache
   * @param RedirectHelper $seoRedirect
   *
   * @return RedirectedUrlCreator
   */
  public static function init($baseUrl, $cache = null, $seoRedirect = null)
  {
    return new self($baseUrl, $cache, $seoRedirect);
  }

  /**
   * @return string
   */
  public function create()
  {
    $this->setTargetFromCache();

    if( empty($this->target) )
    {
      $this->setTargetFromRedirects();
      $this->setToCollection();
      $this->setToCache();
    }

    return $this->target;
  }

  protected function setTargetFromRedirects()
  {
    $this->seoRedirect->setReplaceMode(true);

    $this->seoRedirect->setCurrentUrl($this->baseUrl);
    $this->seoRedirect->find();

    $this->target = $this->seoRedirect->isRedirect ? $this->seoRedirect->targetUrl : $this->baseUrl;
  }

  protected function setToCollection()
  {
    if( isset(Yii::app()->urlCollection) && Yii::app()->urlCollection->collectUrls === true )
    {
      Yii::app()->urlCollection->push($this->target);
    }
  }

  /**
   * @return string
   */
  protected function getCacheId()
  {
    return self::CACHE_PREFIX.$this->baseUrl;
  }

  protected function setTargetFromCache()
  {
    if( $this->useCache() && $this->cache->offsetExists($this->getCacheId()) )
    {
      $this->target = $this->cache->offsetGet($this->getCacheId());
    }
  }

  protected function setToCache()
  {
    if( $this->useCache() )
    {
      $this->cache->offsetSet($this->getCacheId(), $this->target);
    }
  }

  /**
   * @return bool
   */
  protected function useCache()
  {
    return isset(Yii::app()->seoRedirect) && Yii::app()->seoRedirect->cacheUrls === true && $this->cache !== null;
  }
}