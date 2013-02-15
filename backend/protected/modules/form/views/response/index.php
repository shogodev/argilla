<?php
/* @var BResponseController $this */
/* @var BResponse $model  */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'date', 'class' => 'BDatePickerColumn', 'type' => 'datetime', 'value' => 'strtotime($data->date)'),
    array('name' => 'name', 'filter' => false),

    array('name' => 'email'),

    array('header' => 'Продукт', 'value' => '$data->product->name'),

    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),
    array('class' => 'BButtonColumn'),
  ),
));