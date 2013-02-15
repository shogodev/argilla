<?php
/* @var $this BProductTypeController */
/* @var $dataProvider BActiveDataProvider */
/* @var $model BProductType */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'position', 'class' => 'OnFlyEditField', 'htmlOptions' => array('class' => 'span1'), 'header' => 'Позиция'),
    array('name' => 'name', 'filter' => false),

    array('name' => 'section_id', 'value' => '$data->section ? $data->section->name : ""', 'filter' => CHtml::listData(BProductSection::model()->findAll(), 'id', 'name')),

    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
));