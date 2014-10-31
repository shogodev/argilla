<?php

/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.share.behaviors
 */
class CommonBehavior extends CBehavior
{
  private $settings;

  /**
   * @var Contact
   */
  private $headerContacts;

  public function attach($owner)
  {
    parent::attach($owner);

    Yii::import('frontend.models.contact.*');
    Yii::import('frontend.models.Settings');
  }

  /**
   * @return FActiveRecord|Contact
   */
  public function getHeaderContacts()
  {
    if( !isset($this->headerContacts) )
    {
      $this->headerContacts = Contact::model()->findByAttributes(array('sysname' => 'header'));
    }

    return $this->headerContacts;
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