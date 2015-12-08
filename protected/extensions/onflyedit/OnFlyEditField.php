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

  public $gridUpdate = false;

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

    if( empty($this->ajaxUrl) )
      $this->ajaxUrl = Yii::app()->controller->createUrl(Yii::app()->controller->id."/$this->action");

    Yii::app()->clientScript->registerScript(
      'initOnFly'.$this->grid->id,
      '$(function() {
        Backend("onFly", function(box) {
          box.init(jQuery);
          jQuery.fn.yiiGridView.addObserver("'.$this->grid->id.'", function(id) { box.reinstall(jQuery); });
        });
      });',
      CClientScript::POS_END
    );
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
    $htmlOptions = CMap::mergeArray(
      array(
        'data-grid-id' => $this->gridId,
        'data-grid-update' => $this->gridUpdate
      ),
      $this->elementOptions
    );

    $primaryKey = $data instanceof BActiveRecord ? $data->getPrimaryKey() : $data['id'];

    Yii::app()->controller->widget('OnFlyWidget', array(
      'ajaxUrl' => $this->ajaxUrl,
      'attribute' => $this->name,
      'primaryKey' => $primaryKey,
      'value' => $data[$this->name],
      'htmlOptions' => $htmlOptions
    ));
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
      'data-grid-update' => $this->gridUpdate
    ];

    if( empty($this->dropDown) )
    {
      return CHtml::tag('span', array_merge($commonAttributes, ['class' => 'onfly-edit'], $this->elementOptions), $value);
    }
    else
    {
      return CHtml::dropDownList('', $value, $this->dropDown,
        array_merge($commonAttributes, ['class' => 'onfly-edit-dropdown', 'style' => 'margin-bottom: 0px; width: auto;'], $this->elementOptions));
    }
  }
}