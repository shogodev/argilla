<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models
 *
 * @method static Banner model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property integer $position
 * @property integer $location
 * @property string $title
 * @property string $url
 * @property string $img
 * @property integer $swd_w
 * @property integer $swd_h
 * @property string $code
 * @property string $pagelist
 * @property string $pagelist_exc
 * @property boolean $new_window
 * @property boolean $visible
 *
 * @property FSingleImage $image
 */
class Banner extends FActiveRecord
{
  protected $banners;

  protected $bannersUrl;

  public function behaviors()
  {
    return array(
      'imageBehavior' => array('class' => 'SingleImageBehavior', 'path' => 'images'),
    );
  }

  public function defaultScope()
  {
    $alias = $this->getTableAlias(false, false);

    return array(
      'condition' => $alias.'.visible=1',
      'order' => $alias.'.position',
    );
  }

  /**
   * @param string $location
   *
   * @return Banner[]
   */
  public function getByLocationAll($location)
  {
    if( !isset($this->banners[$location]) )
      $this->banners[$location] = $this->findAllByAttributes(array('location' => $location));

    return $this->banners[$location];
  }

  /**
   * @param string $location
   *
   * @return Banner
   */
  public function getByLocation($location)
  {
    return Arr::reset($this->getByLocationAll($location));
  }

  /**
   * @param null|string $location
   *
   * @return mixed
   */
  public function getByCurrentUrlAll($location = null)
  {
    $url = $this->getPrepareUrl(Yii::app()->controller->getCurrentUrl());

    if( !isset($this->bannersUrl[$url]) )
    {
      /**
       * @var Banner[] $banners
       */
      if( isset($location) )
        $banners = $this->getByLocationAll($location);
      else
        $banners = $this->findAll();

      $this->bannersUrl[$location][$url] = array();
      foreach($banners as $banner)
      {
        if( $this->containUrl($banner->pagelist, $url) && !$this->containUrl($banner->pagelist_exc, $url) )
          $this->bannersUrl[$location][$url][] = $banner;
      }
    }

    return $this->bannersUrl[$location][$url];
  }

  /**
   * @param null|string $location
   *
   * @return mixed
   */
  public function getByCurrentUrl($location = null)
  {
    return Arr::reset($this->getByCurrentUrlAll($location));
  }

  /**
   * @param array $imageOptions
   * @param array $linkOptions
   */
  public function render($imageOptions = array(), $linkOptions = array())
  {
    $image = CHtml::image($this->image, '', $imageOptions);

    if( $this->new_window )
      $linkOptions['target'] = '_blank';

    echo CHtml::link($image, $this->url, $linkOptions);
  }

  private function containUrl($urlList, $url)
  {
    if( empty($urlList) )
      return false;

    foreach(explode("\n", $urlList) as $searchPage)
    {
      $pattern = '#^'.str_replace('*', '.*', trim($searchPage)).'$#';

      if( preg_match($pattern, $url, $matches) )
        return true;
    }

    return false;
  }

  private function getPrepareUrl($url)
  {
    return preg_replace('/\?.*/', '', $url);
  }
}