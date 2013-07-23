<?php
/**
 * @var Order $model
 */
?>
<?php
$fields = array('Номер заказа' => $model->id);

foreach($model->attributeLabels() as $attribute => $label)
  if( !empty($model->$attribute) )
    $fields[$label] = $model->$attribute;

if( $model->payment )
  $fields['Методы оплаты'] = $model->payment->name;

if( $model->delivery )
  $fields['Способ доставки'] = $model->delivery->name;

$data = array(
  'header' => "Заказ",
  'fields' => $fields,
  'bottom' => Yii::app()->controller->renderPartial('frontend.views.email.orderProducts', $_data_),
);
?>

<?php echo Yii::app()->controller->renderPartial('frontend.views.email._backendTemplate', CMap::mergeArray($_data_, $data)); ?>
