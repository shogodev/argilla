<?php
/* @var BColorController $this */
/* @var BColor $model */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'name'),
    array('name' => 'variant_id', 'value' => '$data->variant ? $data->variant->name : ""', 'filter' => CHtml::listData(BColor::model()->getVariants(), 'id', 'name')),
    array('name' => 'img', 'class' => 'BImageColumn', 'filter' => false),
    array('class' => 'BButtonColumn'),
  ),
));