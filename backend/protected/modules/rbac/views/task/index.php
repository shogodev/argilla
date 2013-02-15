<?php
/* @var $this TaskController */
/* @var $dataProvider BActiveDataProvider */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $this->widget('BGridView', array(
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'title', 'header' => 'Название', 'htmlOptions' => array('class' => 'center span1')),
    array('name' => 'name', 'header' => 'Системное имя', 'htmlOptions' => array('class' => 'center span1')),
    array('name' => 'description', 'header' => 'Описание', 'htmlOptions' => array('class' => 'center span1')),

    array('class' => 'BButtonColumn'),
  ),
)); ?>