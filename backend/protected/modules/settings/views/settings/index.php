<?php
/* @var BSettingsController $this */
/* @var BSettings $model */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'param', 'htmlOptions' => array('class' => 'span3')),
    array('name' => 'value', 'htmlOptions' => array('class' => 'span3')),
    array('name' => 'notice', 'filter' => false),

    array('class' => 'BButtonColumn'),
  ),
));