<?php
/**
 * @var BController $this
 * @var BFrontendMenu $model
 * @var BActiveDataProvider $dataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'dataProvider' => $dataProvider,
  'filter' => $model,
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'name'),
    array('name' => 'sysname'),
    array('name' => 'url'),

    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => false),
    array('class' => 'BButtonColumn'),
  ),
));