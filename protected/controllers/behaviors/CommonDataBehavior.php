<?php

/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers.behaviors
 *
 * @property array counters
 * @property array copyrights
 */
class CommonDataBehavior extends CBehavior
{
  /**
   * @var TextBlock[] $textBlocks
   */
  protected $textBlocks = array();

  /**
   * @param $key
   *
   * @return TextBlock
   */
  public function textBlock($key)
  {
    return Arr::reduce($this->textBlocks($key));
  }

  /**
   * @param $key
   *
   * @return TextBlock[]
   */
  public function textBlocks($key)
  {
    if( !isset($this->textBlocks[$key]) )
      $this->textBlocks[$key] = TextBlock::model()->findAllByAttributes(array('location' => $key));

    return $this->textBlocks[$key];
  }

  /**
   * @return Counter[]
   */
  public function getCounters()
  {
    $main = false;
    if( $this->owner->action->id === $this->owner->id && $this->owner->id === 'index' )
      $main = true;

    $criteria = new CDbCriteria();
    $criteria->compare('main', ($main ? '=' : '<>').'1');

    return Counter::model()->findAll($criteria);
  }

  public function getCopyrights($key = 'copyright')
  {
    $url        = Yii::app()->request->requestUri;
    $copyrights = LinksBlock::model()->getLinks($key, $url);

    return $copyrights;
  }

  public function getContacts()
  {
    return array(
      'phones' => ContactGroup::getByKey('phones')->fields,
      'icq'    => ContactGroup::getByKey('icq')->fields,
    );
  }
}