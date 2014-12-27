<?php
/**
* @var BProductTextController $this
* @var BProductText $model
*/
Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'url', 'class' => 'BEditColumn'),
    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),
    array('class' => 'BButtonColumn'),
  ),
));