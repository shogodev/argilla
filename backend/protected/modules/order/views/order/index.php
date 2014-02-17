<?php
/**
 * @var BOrderController $this
 * @var BOrder $model
 * @var BActiveDataProvider $dataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter'       => $model,
  'dataProvider' => $dataProvider,
  'columns'      => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'date_create', 'class' => 'BDatePickerColumn'),

    array(
      'name' => 'user_id',
      'value' => '$data->user ? $data->user->getFullName() : $data->name',
      'class' => 'BOrderUserColumn',
      'iframeAction' => '/user/BFrontendUser/update',
      'htmlOptions' => array('class' => 'span3')
    ),

    array('name' => 'comment', 'filter' => false, 'value' => 'Utils::stripText($data->comment, 50)'),
    array('name' => 'type', 'htmlOptions' => array('class' => 'span2'), 'value' => '$data->typeLabel[$data->type]', 'filter' => false),
    array('name' => 'sum', 'class' => 'BOrderPopupColumn', 'type' => 'number', 'iframeAction' => '/order/BOrder/orderProducts'),

    array(
      'name' => 'status_id',
      'class' => 'OnFlyEditField',
      'dropDown' => BOrderStatus::model()->listData(),
      'value' => '$data->status ? $data->status->name : ""',
      'filter' => BOrderStatus::model()->listData(),
    ),

    array('class' => 'BOrderButtonColumn'),
  ),
));