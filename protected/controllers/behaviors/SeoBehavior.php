<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers.behaviors
 */
class SeoBehavior extends CBehavior
{
  /**
   * @return Counter[]
   */
  public function getCounters()
  {
    $criteria = new CDbCriteria();

    if( !($this->owner->action->id === $this->owner->id && $this->owner->id === 'index') )
      $criteria->compare('main', '<>1');

    return Counter::model()->findAll($criteria);
  }

  public function getCopyrights($key = 'copyright')
  {
    $copyrights = LinkBlock::model()->getLinks($key, Yii::app()->request->requestUri);

    return $copyrights;
  }

  public function getCopyright($key = 'copyright')
  {
    if( $copyrights = $this->getCopyrights($key) )
      return Arr::reset($copyrights);

    return '';
  }

  /**
   * @param string $url
   * @param string $title
   * @param string $image
   * @param string $description
   *
   * Example:
   * $this->registerSocialMeta($model->getUrl(true), $model->getHeader(), $model->getImage(), $model->notice);
   */
  public function registerSocialMeta($url, $title, $image, $description)
  {
    $tags = array(
      'og:url' => $url,
      'og:title' => $title,
      'og:site_name' => Yii::app()->name,
      'og:image' => $image,
      'og:description' => $description,
    );

    if( $clientScript = Yii::app()->clientScript )
      foreach($tags as $tag => $content)
        $clientScript->registerMetaTag($content, null, null, array('property' => $tag));
  }
}