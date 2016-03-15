<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class OnFlyWidget extends CWidget
{
  const TYPE_INPUT = 'input';

  const TYPE_DROPDOWN = 'dropDown';

  /**
   * URL для AJAX запроса.
   *
   * @var string
   */
  public $ajaxUrl;

  /**
   * @var string $type тип (OnFlyWidget::TYPE_INPUT, OnFlyWidget::TYPE_DROPDOWN)
   */
  public $type;

  public $items = array();

  public $attribute;

  public $primaryKey;

  public $value;

  public $htmlOptions = array();

  public function init()
  {
    if( !isset($this->ajaxUrl, $this->attribute, $this->primaryKey) )
      throw new RequiredPropertiesException(__CLASS__, array('ajaxUrl', 'attribute', 'primaryKey', 'type'));

      self::registerOnFlyScripts();
  }

  public function run()
  {
    $htmlOptions = CMap::mergeArray($this->htmlOptions, array(
      'data-onflyedit' => implode('-', array($this->attribute, $this->primaryKey)),
      'data-ajax-url' => $this->ajaxUrl,
    ));

    switch($this->type)
    {
      case self::TYPE_INPUT;
        $htmlOptions['class'] = 'onfly-edit';
        $html = CHtml::tag('span', $htmlOptions, $this->value);
      break;

      case self::TYPE_DROPDOWN;
        $htmlOptions['class'] = 'onfly-edit-dropdown';
        $htmlOptions['style'] = 'margin-bottom: 0px; width: auto;';

        $html = CHtml::dropDownList('', $this->value, $this->items, $htmlOptions);
      break;
    }

    echo $html;
  }

  public static function registerOnFlyScripts()
  {
    $scriptUrl = Yii::app()->assetManager->publish(dirname(__FILE__).'/js');
    Yii::app()->clientScript->registerScriptFile($scriptUrl.'/jquery.onFlyEdit.js', CClientScript::POS_END);
    Yii::app()->clientScript->registerScriptFile($scriptUrl.'/onFlyModule.js', CClientScript::POS_END);
  }
}