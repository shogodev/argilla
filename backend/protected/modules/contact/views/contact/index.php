<?php
/**
 * @var BContactController $this
 * @var BContact $model
 * @var BActiveDataProvider $dataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'name'),
    array('name' => 'sysname', 'htmlOptions' => array('class' => 'span3')),
    array('name' => 'url', 'htmlOptions' => array('class' => 'span3')),
    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
));