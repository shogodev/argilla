<?php
/* @var BGridSettingsController $this */
/* @var BGridSettings $model */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns' => array(
    array('class' => 'BPkColumn'),
    array('name' => 'position', 'class' => 'OnFlyEditField', 'htmlOptions' => array('class' => 'span1'), 'header' => 'Позиция', 'filter' => false),
    array('name' => 'name', 'class' => 'BEditColumn', 'htmlOptions' => array('class' => 'span5')),
    array('class' => 'JToggleColumn', 'name' => 'filter'),
    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
));