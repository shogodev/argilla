<?php
/* @var BProductCurrencyController $this */
/* @var BActiveDataProvider $dataProvider */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'name', 'class' => 'BEditColumn'),
    array('name' => 'title', 'type' => 'html'),
    array('name' => 'rate'),
    array('name' => 'multiplier'),

    array('class' => 'BButtonColumn'),
  ),
));