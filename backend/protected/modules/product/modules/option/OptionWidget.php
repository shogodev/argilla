<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Пример подключения
 * <div>
 *  <?php $this->widget('option.OptionWidget', array('model' => $model))?>
 * </div>
 */
Yii::import('option.models.*');
/**
 * Class OptionWidget
 */
class OptionWidget extends CWidget
{
  /**
   * @var BProduct $model
   */
  public $model;

  public $header = 'Опции';

  private $options;

  public function init()
  {
    if( is_null($this->model) )
      throw new CHttpException(500, 'Укажите свойство model для виджета '.__CLASS__);

    if( $this->isAvailable() )
      $this->options = BOption::model()->getByProductId($this->model->id);
  }

  public function run()
  {
    if( !$this->isAvailable() )
      return;

    echo '<tr><th><label>'.$this->header.'</label></th><td>';
    $this->renderGrid();
    echo '</td></tr>';
  }

  private function renderGrid()
  {
    $updateButtonClass = 'edit_option_button';

    $optionColumns = array(
      array(
        'name' => 'position',
        'class' => 'OnFlyEditField',
        'ajaxUrl' => Yii::app()->controller->createUrl('option/option/onflyedit'),
        'header' => 'Позиция',
        'htmlOptions' => array('class' => 'span1'),
      ),
      array('name' => 'name', 'header' => 'Название'),
      array(
        'name' => 'price',
        'header' => 'Цена',
        'class' => 'OnFlyEditField',
        'ajaxUrl' => Yii::app()->controller->createUrl('option/option/onflyedit'),
        'htmlOptions' => array('class' => 'span2'),
      ),
      array(
        'class' => 'BButtonColumn',
        'updateButtonUrl' => 'Yii::app()->controller->createUrl("option/option/update", array("id" => $data->primaryKey, "popup" => true))',
        'updateButtonOptions' => array('class' => 'update ' . $updateButtonClass),
        'deleteButtonUrl' => 'Yii::app()->controller->createUrl("option/option/delete", array("id" => $data->primaryKey))'
      ),
    );

    $grid = $this->controller->widget('BGridView', array(
      'dataProvider' => new CArrayDataProvider($this->options, array('pagination' => false)),
      'template' => "{buttons}\n{items}\n{pager}\n{scripts}",
      'filter' => $this->model,
      'buttonsTemplate' => 'option.views._button_add_option',
      'columns' => $optionColumns
    ));

    $this->registerUpdateScript($grid->id, $updateButtonClass);
  }

  private function registerUpdateScript($widgetId, $updateButtonClass)
  {
    Yii::app()->clientScript->registerScript($updateButtonClass.'_script', "
      jQuery(document).on('click', '.{$updateButtonClass}', function(e){
        e.preventDefault();
        assigner.open(this.href, {'updateGridId' : '{$widgetId}'});
      });
    ");
  }

  private function isAvailable()
  {
    return !$this->controller->popup && !$this->model->isNewRecord;
  }
}