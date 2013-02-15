<?php
/**
 * @var BNewsController $this
 * @var BNews $model
 */
Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center'), 'filter' => false),
    array('name' => 'date', 'class' => 'BDatePickerColumn'),

    array(
      'header' => 'Раздел',
      'value' => '$data->section->name',
      'name' => 'section_id',
      'filter' => CHtml::listData(BNewsSection::model()->findAll(), 'id', 'name')
    ),

    array('name' => 'name', 'htmlOptions' => array('class' => 'span3')),
    array('name' => 'notice', 'type' => 'html', 'htmlOptions' => array('class' => 'span4'), 'filter' => false),

    array('class' => 'JToggleColumn', 'name' => 'main', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),
    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),

    array('class' => 'BButtonColumn'),
  ),
));