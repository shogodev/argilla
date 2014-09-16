<?php
/**
 * @var $this BController
 * @var $model BBanner
 * @var $dataProvider BActiveDataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $this->widget('BGridView', array(
  'dataProvider' => $dataProvider,
  'filter' => $model,
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'position', 'class' => 'OnFlyEditField', 'htmlOptions' => array('class' => 'span1'), 'header' => 'Позиция'),
    array('name' => 'title', 'header' => 'Название'),

    array('name' => 'location'),
    array('name' => 'img', 'class' => 'BImageColumn', 'filter' => false),

    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
)); ?>