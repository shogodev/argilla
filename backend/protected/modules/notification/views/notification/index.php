<?php
/**
 * @var $this BNotificationController
 * @var $model BNotification
 * @var $dataProvider CActiveDataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'dataProvider' => $dataProvider,
  'filter' => $model,
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'name', 'value' => '!empty($data->name) ? $data->name : $data->index'),
    array('name' => 'email'),
    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
));