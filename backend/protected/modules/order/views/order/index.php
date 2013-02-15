<?php
/* @var $this BOrderController */
/* @var $model BOrder */

Yii::app()->breadcrumbs->show();
?>

<?php $this->widget('BGridView', array(
  'dataProvider' => $model->search(),
  'filter'       => $model,
  'columns'      => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1')),
    array('name' => 'date_create', 'type' => 'datetime', 'value' => 'strtotime($data->date_create)', 'class' => 'BDatePickerColumn'),
    array('name' => 'status', 'htmlOptions' => array('class' => 'center span1'), 'value' => '$data->statusLabel[$data->status]', 'filter' => $model->statusLabel),
    array('name' => 'type', 'htmlOptions' => array('class' => 'center span1'), 'value' => '$data->typeLabel[$data->type]', 'filter' => $model->typeLabel),
    array('name' => 'name'),
    array('name' => 'email'),
    array('name' => 'sum'),
    array('class' => 'BButtonColumn'),
  ),
)); ?>