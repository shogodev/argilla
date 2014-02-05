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

  private $settings;

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
   * Если текстовый блок не существует, то он создается
   * @param string|null $name - название
   * @param string|null $content - текст по умолчанию
   * @param array('class' => 'success-message') $htmlOptions
   * @return string
   */
  public function textBlockRegister($name = null, $content = null, $htmlOptions = array('class' => 'success-message'))
  {
    if( $name === null )
      $location = $this->owner->route;
    else
      $location = Utils::translite($name);

    if( $this->textBlock($location) === false )
    {
      $textBlock = new TextBlock();
      $textBlock->attributes = array(
        'location' => $location,
        'name' => empty($name) ? $this->owner->route : $name.' ('.$this->owner->route.')',
        'visible' => 1,
        'auto_created' => 1
      );

      if( $content !== null )
        $textBlock->setAttribute('content', $htmlOptions != null ? CHtml::tag('div', $htmlOptions, $content) : $content);

      $textBlock->save();

      if( $content === null )
      {
        $textBlock->content = '<div style="background-color: red; padding: 10px; color: white; font-size: 12px; font-weight: bold">';
        $textBlock->content .= 'Данный текстовый блок сгенерирован автоматически в '.$this->owner->route.'.';
        $textBlock->content .= 'Текст нужно заменить в <a href="/backend/textblock/bTextBlock/update/'.$textBlock->id.'">backend</a>';
        $textBlock->content .= '</div>';
        $textBlock->save();
      }

      $this->textBlocks[$location] = $textBlock;
    }

    return $this->textBlock($location)->content;
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

  public function getContacts()
  {
    if( $this->contacts === null )
    {
      $this->contacts = Contact::model()->findAll();
    }

    return $this->contacts;
  }

  /**
   * Возвращает одно значение настройки или все
   * @param null $key если указан, то возвращается настройка с соответстующим ключом, иначе возвращает все настройки
   * @param null $defaultValue
   *
   * @return array|string|null
   */
  public function getSettings($key = null, $defaultValue = null)
  {
    if( $this->settings === null )
    {
      $this->settings = array();
      $settings = Settings::model()->findAll();

      foreach($settings as $setting)
        $this->settings[$setting->param] = $setting->value;
    }

    if( $key === null )
      return $this->settings;

    return Arr::get($this->settings, $key, $defaultValue);
  }
}