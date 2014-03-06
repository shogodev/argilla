<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.widgets
 */
class CaptchaWidget extends CWidget
{
  public $model;

  public $attribute;

  public $layout = '{input}';

  public $form;

  public function run()
  {
    echo CHtml::openTag('span', array('class' => 'captcha-container'));
    $this->widget('CCaptcha', array(
      'showRefreshButton' => true,
      'clickableImage' => true,
      'imageOptions' => array('style' => 'cursor: pointer'),
      'buttonOptions' => array('class' => 'refresh-button'),
      'buttonLabel' => 'обновить изображение',
    ));
    echo CHtml::closeTag('span');
    echo CHtml::activeTextField($this->model, $this->attribute, array('class' => 'inp', 'style' => 'width: 200px'));
  }
}