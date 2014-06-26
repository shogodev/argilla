<?php
/**
 * @var BGridSettingsController $this
 * @var BGridSettings $model
 * @var BActiveDataProvider $dataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('class' => 'BPkColumn'),
    array('name' => 'position', 'class' => 'OnFlyEditField', 'htmlOptions' => array('class' => 'span1'), 'header' => 'Позиция', 'filter' => false),
    array('name' => 'name', 'class' => 'BEditColumn', 'htmlOptions' => array('class' => 'span5')),
    array('name' => 'filter',  'htmlOptions' => array('class' => 'span1'), 'value' => 'BGridSettings::$labels[$data->filter]'),

    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
));