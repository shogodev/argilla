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
if( isset($model->paymentType) )
  $fields['Методы оплаты'] = $model->paymentType->name;
if( isset($model->deliveryType) )
  $fields['Способ доставки'] = $model->deliveryType->name;
?>

<?php $this->renderPartial('frontend.views.email.defaultBackend', CMap::mergeArray($_data_, array('fields' => $fields))); ?>

<div style="margin-bottom: 20px">
  <?php $this->renderPartial('frontend.views.email._order_products', $_data_)?>
</div>