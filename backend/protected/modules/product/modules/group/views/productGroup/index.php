<?php
/**
 * @var BProductGroupController $this
 * @var BActiveDataProvider $dataProvider
 * @var BProductGroup $model
 */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'position', 'class' => 'OnFlyEditField', 'filter' => false),
    array('name' => 'name'),
    array(
      'name' => 'BProduct',
      'header' => 'Продукты',
      'class' => 'BPopupColumn',
      'iframeAction' => '/product/product/index',
    ),

    array('class' => 'BButtonColumn'),
  ),
));