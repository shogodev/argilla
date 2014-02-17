<?php
/**
 * @var BVacancyController $this
 * @var BVacancy $model
 * @var BActiveDataProvider $dataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'date', 'class' => 'BDatePickerColumn'),
    array('name' => 'name', 'htmlOptions' => array()),
    array('name' => 'phone'),
    array('name' => 'content', 'htmlOptions' => array(), 'filter' => false),
    array('class' => 'BButtonColumn'),
  ),
)); ?>