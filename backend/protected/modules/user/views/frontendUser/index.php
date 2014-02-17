<?php
/**
 * @var UserController $this
 * @var BActiveDataProvider $dataProvider
 * @var BActiveRecord $model
 */
?>

<?php Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $dataProvider,
  'columns'      => array(
    array('name' => 'id', 'class' => 'BPkColumn', 'ajaxUrl' => false),
    array('name' => 'date_create', 'type' => 'datetime', 'value' => 'strtotime($data->date_create)', 'filter' => false),
    array('name' => 'login'),
    array('name' => 'fullName', 'value' => '$data->getFullName()', 'header' => 'Имя'),
    array('name' => 'email'),
    array('name' => 'userPhone', 'value' => '$data->user ? $data->user->phone : ""'),
    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonUpdateColumn'),
  ),
));