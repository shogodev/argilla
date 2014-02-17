<?php
/**
 * @var BNewsController $this
 * @var BNews $model
 * @var BActiveDataProvider $dataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'date', 'class' => 'BDatePickerColumn'),

    array(
      'header' => 'Раздел',
      'value' => '$data->section->name',
      'name' => 'section_id',
      'filter' => CHtml::listData(BNewsSection::model()->findAll(), 'id', 'name')
    ),

    array('name' => 'name', 'htmlOptions' => array('class' => 'span3')),
    array('name' => 'notice', 'type' => 'html', 'htmlOptions' => array('class' => 'span4'), 'filter' => false),

    array('class' => 'JToggleColumn', 'name' => 'main'),
    array('class' => 'JToggleColumn', 'name' => 'visible'),

    array('class' => 'BButtonColumn'),
  ),
));