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
   * @param string $baseUrl
   *
   * @return RedirectedUrlCreator
   */
  public static function init($baseUrl)
  {
    return new self($baseUrl);
  }

  /**
   * @param string $baseUrl
   */
  private function __construct($baseUrl)
  {
    $this->baseUrl = $baseUrl;
  }

  /**
   * @return string
   */
  public function create()
  {
    $this->getFromCache();

    if( empty($this->target) )
    {
      $seoRedirect = Yii::app()->seoRedirect;

      $seoRedirect->setReplaceMode(true);
      $seoRedirect->setCurrentUrl($this->baseUrl);
      $seoRedirect->find();

      $this->target = $seoRedirect->isRedirect ? $seoRedirect->targetUrl : $this->baseUrl;

      if( !empty(Yii::app()->params['collectUrls']) && Yii::app()->params['collectUrls'] === true )
        Yii::app()->urlCollection->push($this->target);

      $this->setToCache();
    }

    return $this->target;
  }

  /**
   * @return int
   */
  protected function getCacheExpire()
  {
    return YII_DEBUG ? 0 : 1800;
  }

  /**
   * @return string
   */
  protected function getCacheId()
  {
    return self::CACHE_PREFIX.$this->baseUrl;
  }

  protected function getFromCache()
  {
    if( $this->useCache() && Yii::app()->cache->offsetExists($this->getCacheId()) )
      $this->target = Yii::app()->cache->offsetGet($this->getCacheId());

  }

  protected function setToCache()
  {
    if( $this->useCache() )
      Yii::app()->cache->offsetSet($this->getCacheId(), $this->target);
  }

  /**
   * @return bool
   */
  protected function useCache()
  {
    return Yii::app()->params['cacheUrls'] === true && Yii::app()->cache !== null;
  }
}