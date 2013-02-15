<?php
/* @var BCallbackController $this */
/* @var BCallback $model */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'date', 'class' => 'BDatePickerColumn'),
    array('name' => 'name', 'htmlOptions' => array()),
    array('name' => 'phone','htmlOptions' => array()),
    array('name' => 'time','htmlOptions' => array(), 'filter' => false),
    array('name' => 'content','htmlOptions' => array(), 'filter' => false),
    array('name' => 'result','htmlOptions' => array(), 'filter' => false),
    array('class' => 'BButtonColumn'),
  ),
));