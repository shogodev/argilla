<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.widgets
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
    ), false, false);
    echo CHtml::closeTag('div');
    echo CHtml::closeTag('div');
  }
}