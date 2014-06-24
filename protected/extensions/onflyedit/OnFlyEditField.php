<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
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

  public $elementOptions = array();

  public $action = 'onflyedit';

  /**
   * URL для AJAX запроса.
   *
   * @var string
   */
  public $ajaxUrl;

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
    if( empty($this->ajaxUrl) )
      $this->ajaxUrl = Yii::app()->controller->createUrl(Yii::app()->controller->id."/$this->action");

    $scriptUrl = Yii::app()->assetManager->publish(dirname(__FILE__).'/js');

    Yii::app()->clientScript->registerScriptFile($scriptUrl.'/jquery.onFlyEdit.js', CClientScript::POS_END);
    Yii::app()->clientScript->registerScriptFile($scriptUrl.'/onFlyModule.js', CClientScript::POS_END);

    Yii::app()->clientScript->registerScript('initOnFly'.$this->grid->id,
      '$(function() {
        Backend("onFly", function(box) {
          box.init(jQuery);
          jQuery.fn.yiiGridView.addObserver("'.$this->grid->id.'", function(id) { box.reinstall(jQuery); });
        });
      });', CClientScript::POS_END);
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
    {
      $name = $this->name;
    }

    $commonAttributes = [
      'data-onflyedit' => $name.'-'.$fieldID,
      'data-ajax-url' => $this->ajaxUrl,
      'data-grid-id' => $this->gridId,
    ];

    if( empty($this->dropDown) )
    {
      return CHtml::tag('span', array_merge($commonAttributes, ['class' => 'onfly-edit'], $this->elementOptions), $value);
    }
    else
    {
      return CHtml::dropDownList('', $value, $this->dropDown,
        array_merge($commonAttributes, ['class' => 'onfly-edit-dropdown', 'style' => 'margin-bottom: 0px;'], $this->elementOptions));
    }
  }
}