<?php
/* @var BProductSectionController $this */
/* @var BActiveDataProvider $dataProvider */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1')),
    array('name' => 'position', 'class' => 'OnFlyEditField', 'htmlOptions' => array('class' => 'span1'), 'header' => 'Позиция'),
    array('name' => 'name'),

    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
));