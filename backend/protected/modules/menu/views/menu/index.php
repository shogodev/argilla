<?php
/**
 * @var $this BController
 * @var $dataProvider BActiveDataProvider
 * @var $model BFrontendMenu
 */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'dataProvider' => $model->search(),
  'filter' => $model,
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'name'),
    array('name' => 'sysname'),
    array('name' => 'url'),

    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => false),
    array('class' => 'BButtonColumn'),
  ),
));