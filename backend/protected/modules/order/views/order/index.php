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
      'name' => 'userProfile',
      'value' => '$data->user ? $data->user->getFullName() : $data->name',
      'attribute' => 'user_id',
      'class' => 'BOrderUserColumn',
      'iframeAction' => '/user/BFrontendUser/update',
      'htmlOptions' => array('class' => 'span3')
    ),

    array('name' => 'email', 'filter' => false),
    array('name' => 'phone', 'filter' => false),

    array('name' => 'comment', 'filter' => false, 'value' => 'Utils::stripText($data->comment, 50)'),
    array('name' => 'type', 'htmlOptions' => array('class' => 'span2'), 'value' => '$data->typeLabel[$data->type]', 'filter' => false),
    array('name' => 'totalSum', 'class' => 'BOrderPopupColumn', 'type' => 'number', 'iframeAction' => '/order/BOrder/orderProducts', 'filter' => false),

    array(
      'name' => 'status_id',
      'class' => 'OnFlyEditField',
      'dropDown' => BOrderStatus::model()->listData('id', 'name', new CDbCriteria(array('order' => 'id'))),
      'value' => '$data->status ? $data->status->name : ""',
      'filter' => BOrderStatus::model()->listData('id', 'name', new CDbCriteria(array('order' => 'id'))),
    ),

    array(
      'class' => 'BOrderButtonColumn',
      'template' => '{setUser} {print} {update} {delete}',
      'buttons' => array(
        'print' => array(
          'label' => 'Распечатать заказ',
          'icon' => 'print',
          'url' => 'Yii::app()->controller->createUrl("/order/bOrder/print", array(
          "id" => $data->id
        ))',
        'options' => array(
          'class' => 'print',
          "target" => "_blank",
        ))
      )
    ),
  ),
));