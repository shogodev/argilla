<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Пример подключения
 * <div>
 * <?php $this->widget('color.ColorWidget', array('modelId' => $model->id));?>
 * </div>
 */
Yii::import('color.models.*');
/**
 * Class ColorWidget
 */
class ColorWidget extends RowGridWidget
{
  public $header = 'Цвета';

  public $modelName = 'BProductColor';

  public $modelAttribute = 'product_id';

  public $urlRoute = '//product/color/productColor';

  public $templateButtonColumn = '{delete}';

  public $ajaxUpdateIds = array('product-parameters');

  public function init()
  {
    parent::init();

    Yii::app()->clientScript->registerScript($this->getId().'_afterColorsUpdate', "
    jQuery.fn.yiiGridView.addObserver('".$this->getId()."', function(id, data){
      $('#product-parameters').html($(data).find('#product-parameters'));
      $('#BProduct_product_img-files').html($(data).find('#BProduct_product_img-files'));
    });
    ", CClientScript::POS_READY);
  }

  protected function getColumns()
  {
    return array(
      array('name' => 'id', 'class' => 'BPkColumn'),
      array(
        'name' => 'position',
        'class' => 'OnFlyEditField',
        'ajaxUrl' => Yii::app()->controller->createUrl($this->urlRoute.'/onflyedit'),
        'htmlOptions' => array('class' => 'span1'),
        'header' => 'Позиция'
      ),
      array('name' => 'color_id', 'value' => '$data->color->image', 'type' => 'html', 'header' => 'Цвет', 'htmlOptions' => array('class' => 'center span1')),
      array('name' => 'name', 'value' => '$data->color->name', 'header' => 'Заголовок'),
      array(
        'class' => 'JToggleColumn',
        'name' => 'visible',
        'filter' => CHtml::listData($this->model->yesNoList(), 'id', 'name'),
        'action' => $this->urlRoute.'/toggle'
      ),
    );
  }
}