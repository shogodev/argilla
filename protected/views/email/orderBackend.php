<?php
/**
 * @var CController $this
 *
 * @var Email $sender
 * @var string $subject
 * @var string $host
 * @var string $project
 * @var string $content
 * @var ContactField[] $emails
 * @var ContactField $email
 * @var ContactField[] $phones
 * @var ContactField $phone
 *
 * @var Order $model
 */
?>
<?php
$fields = array('Номер заказа' => $model->id);
if( isset($model->payment, $model->payment->paymentType) )
{
  $fields['Методы оплаты'] = $model->payment->paymentType->name;
  if( isset($model->payment->systemPaymentType) )
    $fields['Тип оплаты'] = $model->payment->systemPaymentType->name;
}
if( isset($model->delivery, $model->delivery->deliveryType) )
{
  $fields['Способ доставки'] = $model->delivery->deliveryType->name;
}
if( isset($model->delivery, $model->delivery->address) )
  $fields['Адрес'] = $model->delivery->address;
?>

<?php $this->renderPartial('frontend.views.email.defaultBackend', CMap::mergeArray($_data_, array('fields' => $fields))); ?>

<div style="margin-bottom: 20px">
  <?php $this->renderPartial('frontend.views.email._order_products', $_data_)?>
</div>