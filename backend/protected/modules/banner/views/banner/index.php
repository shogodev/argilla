<?php
/* @var $this BController */
/* @var $dataProvider BActiveDataProvider */
/* @var $model BBanner */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $this->widget('BGridView', array(
  'dataProvider' => $model->search(),
  'filter' => $model,
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'position', 'class' => 'OnFlyEditField', 'htmlOptions' => array('class' => 'span1'), 'header' => 'Позиция'),
    array('name' => 'title', 'header' => 'Название'),

    array('name' => 'location'),
    array('name' => 'img', 'class' => 'BImageColumn', 'filter' => false),

    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),
    array('class' => 'BButtonColumn'),
  ),
)); ?>
