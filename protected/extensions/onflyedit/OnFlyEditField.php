<?php

Yii::import('zii.widgets.grid.CGridColumn');

/**
 * Класс для получение возможности редактировать поле в CGridView с помощью ajax
 *
 * Класс должен инициализироваться вместе CGridView
 * @example
 * <code>
 *
 * $onFlyEdit = array(
 *  'name'        => 'notice',
 *  'htmlOptions' => array(),
 *  'class'       => 'OnFlyEditField',
 *  'header'      => 'FieldHeader',
 * );
 *
 * $this->widget('bootstrap.widgets.TbGridView', array(
 *  'columns' => array($onFlyEdit,))
 * );
 * </code>
 *
 * @date 22.08.2012
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @package onFlyEdit
 */
class OnFlyEditField extends BDataColumn
{
  /**
   * Имя свойства / поля в базе, используемое для вывода
   *
   * @var string
   */
  public $name;

  /**
   * Значение свойства
   *
   * @var string
   */
  public $value;

  /**
   * id грида
   *
   * @var string
   */
  public $gridId;

  /**
   * Массив значений для вывода поля в виде выпадающего списка
   *
   * @var array|null
   */
  public $dropDown = null;

  /**
   * Инициализация вывода свойства в столбец
   *
   * @override
   *
   * @return void
   */
  public function init()
  {
    parent::init();
    $this->renderScript();
  }

  /**
   * Вывод необходимого скрипта для работы onfly
   *
   * @return void
   */
  protected function renderScript()
  {
    $url_script = Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/js');
    $url_onfly  = Yii::app()->getController()->createUrl(Yii::app()->getController()->id . '/onflyedit');

    if( empty($this->dropDown) )
      $this->renderTextScript($url_script, $url_onfly);
    else
      $this->renderDropDownScript($url_onfly);

  }

  /**
   * Вывод скрипта для выпадающего списка
   *
   * @param string $url_onfly
   */
  protected function renderDropDownScript($url_onfly)
  {
    $js = <<<EOD
$(function(){
  $('select.onfly-edit-dropdown').live('change', function()
  {
    var matches = $(this).attr('data-onflyedit').match(/(\w+)-(\d+)/);
    var data    = {};

    data.action = 'onflyedit';
    data.field  = matches[1];
    data.id     = matches[2];
    data.value  = $(this).val();
    data.gridId = '{$this->gridId}';

    $.post("{$url_onfly}", data, '', 'json');
  });
});
EOD;

    Yii::app()->getClientScript()->registerScript('onflyeditDropDownHandler', $js);
  }

  /**
   * Вывод скриптов для текстового поля
   *
   * @param string $url
   * @param string $url_onfly
   */
  protected function renderTextScript($url, $url_onfly)
  {
    Yii::app()->getClientScript()->registerScriptFile($url.'/jquery.onFlyEdit.js');

    $onflyHandler = <<<EOD
$(function(){
  $('.onfly-edit').onfly({apply : function(elem)
  {
    var matches = $(elem).attr('data-onflyedit').match(/(\w+)-(\d+)/);
    var data    = {};

    data.action = 'onflyedit';
    data.field  = matches[1];
    data.id     = matches[2];
    data.value  = $(elem).text();
    data.gridId = '{$this->gridId}';

    /*function callback( result )
    {
      if( result )
      {
        if( result == '' )
          $(elem).html('[не задано]');
      }
    }*/

    $.post("{$url_onfly}", data, '', 'json');
  }});
});
EOD;
    Yii::app()->getClientScript()->registerScript('onflyeditHandler', $onflyHandler);
  }

  /**
   * Вывод значения свойтва в клетку
   *
   * @override
   *
   * @param int $row
   * @param object $data
   *
   * @return void
   */
  protected function renderDataCellContent($row, $data)
  {
    $field = $this->name;
    echo $this->prepareFieldData($data instanceof BActiveRecord ? $data->getPrimaryKey() : $data['id'], $data[$field]);
  }

  /**
   * Возвращает сгенерированную строку, содержащее значение свойства в теге <span>
   * с нужными параметрами для работы скрипта
   *
   * @param int $fieldID
   * @param string $value
   *
   * @return string
   */
  private function prepareFieldData($fieldID, $value)
  {
    if( empty($this->dropDown) )
      return CHtml::tag('span', array('class' => 'onfly-edit', 'data-onflyedit' => $this->name . '-' . $fieldID), $value);
    else
      return CHtml::dropDownList('', $value, $this->dropDown, array('class' => 'onfly-edit-dropdown', 'data-onflyedit' => $this->name . '-' . $fieldID));
  }
}
