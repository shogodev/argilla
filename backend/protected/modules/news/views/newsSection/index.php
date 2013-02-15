<?php
/* @var BNewsSectionController $this */
/* @var BNewsSection $model */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'position', 'htmlOptions' => array('class' => 'span1'), 'class' => 'OnFlyEditField', 'filter' => false),
    array('name' => 'name', 'htmlOptions' => array()),
    array('name' => 'notice', 'type' => 'html', 'htmlOptions' => array(), 'filter' => false),

    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),
    array('class' => 'BButtonColumn'),
  ),
)); ?>