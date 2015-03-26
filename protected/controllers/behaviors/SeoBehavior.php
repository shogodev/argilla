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
    $url        = Yii::app()->request->requestUri;
    $copyrights = LinkBlock::model()->getLinks($key, $url);

    return $copyrights;
  }
} 