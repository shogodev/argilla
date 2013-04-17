<?php

/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.controllers.behaviors
 */
class CommonDataBehavior extends CBehavior
{
  /**
   * @var TextBlock[] $textBlocks
   */
  protected $textBlocks = array();

  private $contacts;

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

  /**
   * @param string $groupName - имя группы полей
   * @return array|null
   */
  public function getContacts($groupName = null)
  {
    if( !$this->contacts )
    {
      /**
       * @var ContactGroup[] $groups;
       */
      $groups = ContactGroup::model()->findAll('sysname != ""');

      foreach($groups as $group)
        $this->contacts[$group->sysname] = $group->fields;
    }

    return isset($groupName) ? Arr::get($this->contacts, $groupName, null) : $this->contacts;
  }
}