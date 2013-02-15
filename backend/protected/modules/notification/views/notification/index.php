<?php
/* @var $this BNotificationController */
/* @var $dataProvider CActiveDataProvider */
/* @var $model BNotification */
?>
<?php Yii::app()->breadcrumbs->show();?>
<?php $this->widget('BGridView', array(
  'dataProvider' => $model->search(),
  'filter' => $model,
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'name', 'value' => '!empty($data->name) ? $data->name : $data->index'),
    array('name' => 'email'),
    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),
    array('class' => 'BButtonColumn'),
  ),
)); ?>