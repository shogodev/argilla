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
    if( empty($this->target) )
    {
      $seoRedirect = Yii::app()->seoRedirect;

      $seoRedirect->setReplaceMode(true);
      $seoRedirect->setCurrentUrl($this->baseUrl);
      $seoRedirect->find();

      $this->target = $seoRedirect->isRedirect ? $seoRedirect->targetUrl : $this->baseUrl;

      if( !empty(Yii::app()->params['collectUrls']) && Yii::app()->params['collectUrls'] === true )
        Yii::app()->urlCollection->push($this->target);
    }

    return $this->target;
  }
}