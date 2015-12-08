<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class OnFlyWidget extends CWidget
{
  /**
   * URL для AJAX запроса.
   *
   * @var string
   */
  public $ajaxUrl;

  public $items;

  public $attribute;

  public $primaryKey;

  public $value;

  public $htmlOptions = array();

  public function init()
  {
    if( !isset($this->ajaxUrl, $this->attribute, $this->primaryKey) )
      throw new RequiredPropertiesException(__CLASS__, array('ajaxUrl', 'attribute', 'primaryKey'));

    $scriptUrl = Yii::app()->assetManager->publish(dirname(__FILE__).'/js');

    Yii::app()->clientScript->registerScriptFile($scriptUrl.'/jquery.onFlyEdit.js', CClientScript::POS_END);
    Yii::app()->clientScript->registerScriptFile($scriptUrl.'/onFlyModule.js', CClientScript::POS_END);
  }

  public function run()
  {
    $htmlOptions = CMap::mergeArray($this->htmlOptions, array(
      'data-onflyedit' => implode('-', array($this->attribute, $this->primaryKey)),
      'data-ajax-url' => $this->ajaxUrl,
    ));

    if( empty($this->dropDown) )
    {
      $htmlOptions['class'] = 'onfly-edit';

      $result = CHtml::tag('span', $htmlOptions, $this->value);
    }
    else
    {
      $htmlOptions['class'] = 'onfly-edit-dropdown';
      $htmlOptions['style'] = 'margin-bottom: 0px; width: auto;';

      $result = CHtml::dropDownList('', $this->value, $this->items, $htmlOptions);
    }

    echo $result;
  }
}