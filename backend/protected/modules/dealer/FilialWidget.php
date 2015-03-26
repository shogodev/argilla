<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * Пример подключения
 * <div>
 *  <?php $this->widget('filial.FilialWidget', array('model' => $model))?>
 * </div>
 */
/**
 * Class FilialWidget
 */
class FilialWidget extends RowGridWidget
{
  public $header = 'Филиалы';

  protected function getColumns()
  {
    return array(
      array('name' => 'id', 'htmlOptions' => array('class' => 'center span1')),
      array(
        'name' => 'position',
        'class' => 'OnFlyEditField',
        'ajaxUrl' => Yii::app()->controller->createUrl($this->urlRoute.'/onflyedit'),
        'header' => 'Позиция',
        'htmlOptions' => array('class' => 'span1'),
      ),
      array('name' => 'name', 'header' => 'Название'),
      array(
        'value' => 'BDealerCity::model()->findByPk($data->city_id)->name',
        'name' => 'city_id',
        'filter' => BDealerCity::model()->listData('id', 'name'),
      ),
      array('name' => 'address', 'header' => 'Адрес'),
      array('name' => 'coordinates', 'header' => 'Координаты'),
      array(
        'class' => 'JToggleColumn',
        'name' => 'visible',
        'filter' => CHtml::listData($this->model->yesNoList(), 'id', 'name'),
        'action' => $this->urlRoute.'/toggle'
      ),
    );
  }
}