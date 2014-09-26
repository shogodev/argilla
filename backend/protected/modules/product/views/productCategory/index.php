<?php
/* @var BProductCategoryController $this */
/* @var BActiveDataProvider $dataProvider */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'position', 'class' => 'OnFlyEditField'),
    array('name' => 'name'),
    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
));