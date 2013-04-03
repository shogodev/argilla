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

  public $htmlOptions = array('class' => 'span1');

  public $action = 'onflyedit';

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

    Yii::app()->clientScript->registerScriptFile($url_script.'/jquery.onFlyEdit.js');

    if( empty($this->dropDown) )
    {
      $this->renderTextScript($url_script, $url_onfly);
    }
    else
    {
      $this->renderDropDownScript($url_script, $url_onfly);
    }
  }

  /**
   * Вывод скрипта для выпадающего списка
   *
   * @param $url_script
   * @param string $url_onfly
   */
  protected function renderDropDownScript($url_script, $url_onfly)
  {
    Yii::app()->clientScript->registerScriptFile($url_script.'/dropDownOnFlyHandler.js', CClientScript::POS_END);

    $args = CJavaScript::encode([
      'urlToPost' => $url_onfly,
      'gridId' => $this->gridId
    ]);

    Yii::app()->clientScript->registerScript('initDropDownOnFlyEdit',
      "$(function(){bindDropDownOnFlyHandler({$args});})", CClientScript::POS_END);

    Yii::app()->clientScript->registerScript('reinstallDropDownOnFlyEdit',
      "function reinstallDropDownOnFlyEdit(){bindDropDownOnFlyHandler({$args});}", CClientScript::POS_END);
  }

  /**
   * Вывод скриптов для текстового поля
   *
   * @param $url_script
   * @param string $url_onfly
   */
  protected function renderTextScript($url_script, $url_onfly)
  {
    Yii::app()->clientScript->registerScriptFile($url_script.'/textOnFlyHandler.js', CClientScript::POS_END);

    $args = CJavaScript::encode([
      'urlToPost' => $url_onfly,
      'gridId' => $this->gridId
    ]);

    Yii::app()->clientScript->registerScript('initTextOnFlyEdit',
      "$(function(){bindTextOnFlyHandler({$args});})", CClientScript::POS_END);

    Yii::app()->clientScript->registerScript('reinstallTextOnFlyEdit',
      "function reinstallTextOnFlyEdit(){bindTextOnFlyHandler({$args});}", CClientScript::POS_END);
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
   * @param $fieldID
   * @param $value
   * @param null|string $name
   *
   * @return string
   */
  protected function prepareFieldData($fieldID, $value, $name = null)
  {
    if( $name === null )
      $name = $this->name;

    if( empty($this->dropDown) )
      return CHtml::tag('span', array('class' => 'onfly-edit', 'data-onflyedit' => $name . '-' . $fieldID), $value);
    else
      return CHtml::dropDownList('', $value, $this->dropDown, array('class' => 'onfly-edit-dropdown', 'data-onflyedit' => $name . '-' . $fieldID));
  }
}
