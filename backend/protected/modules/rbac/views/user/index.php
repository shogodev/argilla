<?php
/* @var $this UserController */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $this->widget('BGridView', array(
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'username', 'header' => 'Имя пользователя', 'htmlOptions' => array('class' => 'center span1')),

    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
)); ?>