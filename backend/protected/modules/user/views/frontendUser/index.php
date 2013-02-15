<?php
/* @var $this UserController */
/* @var $dataProvider CActiveDataProvider */
/* @var $model CActiveRecord */

Yii::app()->breadcrumbs->show();
?>

<?php $this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns'      => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'date_create', 'type' => 'datetime', 'value' => 'strtotime($data->date_create)', 'filter' => false),
    array('name' => 'login'),
    array('name' => 'fullName', 'value' => '$data->getFullName()', 'header' => 'Имя'),
    array('name' => 'email'),
    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),
    array('class' => 'BButtonUpdateColumn'),
  ),
)); ?>