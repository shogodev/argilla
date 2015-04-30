<?php
/* @var BProductCollectionController $this */
/* @var BActiveDataProvider $dataProvider */
/* @var BProductCollection $model */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'position', 'class' => 'OnFlyEditField', 'filter' => false),
    array('name' => 'name'),
    array('name' => 'parent_id', 'value' => '$data->parent ? $data->parent->name : null', 'filter' => CHtml::listData($model->getParents(), 'id', 'name')),
    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
));