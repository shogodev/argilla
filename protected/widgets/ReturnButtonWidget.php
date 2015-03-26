<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.widgets
 *
 * Виджет кнопка "продолжить покупки" - осуществляет возврат на предыдущую страницу по средствам js
 *
 * Пример:
 * <div>
 *  $this->widget('ReturnButtonWidget', array(
 *    'text' => 'Продолжить покупки',
 *    'htmlOptions' => array('class' => 'btn green-contour-btn')
 *  ));
 * </div>
 */
class ReturnButtonWidget extends CWidget
{
  const CSS_CLASS = 'return-button';

  public $text;

  public $htmlOptions = array();

  public function init()
  {
    $this->htmlOptions['class'] = Arr::get($this->htmlOptions, 'class', '').' '.self::CSS_CLASS;
    $this->registerScript();
  }

  public function run()
  {
    echo CHtml::link($this->text, '#', $this->htmlOptions);
  }

  private function registerScript()
  {
    Yii::app()->clientScript->registerScript(__CLASS__, "
      $('.".self::CSS_CLASS."').on('click', function(e) {
        e.preventDefault();
        history.back();
      });
    ", CClientScript::POS_END);
  }
}