<?php
/**
 * User: Sergey Glagolev <glagolev@shogo.ru>
 * Date: 12.10.12
 */

class YandexShare extends CWidget
{
  public $htmlOptions;

  public $type = 'button';

  public $language = 'ru';

  public $class = 'yashare-auto-init';

  public $services = 'yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,lj';

  public function run()
  {
    Yii::app()->clientScript->registerScriptFile('//yandex.st/share/share.js');

    echo CHtml::tag('div', $this->htmlOptions, false, false);
    echo CHtml::tag('div', array(
      'class' => $this->class,
      'data-yashareL10n' => $this->language,
      'data-yashareType' => $this->type,
      'data-yashareQuickServices' => $this->services,
    ));
    echo CHtml::closeTag('div');
  }
}