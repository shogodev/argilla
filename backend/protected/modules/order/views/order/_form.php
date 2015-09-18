<?php
/**
 * @var BOrderController $this
 * @var BOrder $model
 * @var BOrderDelivery $modelDelivery
 * @var BOrderPayment $modelPayment
 * @var CActiveForm|BActiveForm $form
 * @var array $_data_
 */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId())); ?>

  <table style="width: 100%">
    <tr>
      <td style="width: 50%">
        <?php if( $this->isUpdate() ) {?>
          <div class="s-breadcrumbs breadcrumb" style="font-size: 170%">Заказ №<?php echo $model->id;?> от <?php echo $model->getDate();?> на <span id="js-top-price"><?php echo PriceHelper::price($model->totalSum, ' '.Utils::plural($model->totalSum, 'рубль,рубля,рублей'), '0 рублей')?></span></div>
        <?php }?>
      </td>
      <td style="text-align: right;">
        <?php $this->renderPartial('_form_buttons', array('model' => $model)); ?>
      </td>
    </tr>
  </table>

<?php echo $form->errorSummary($model); ?>

<table class="detail-view table table-striped table-bordered">
  <tbody>
    <?php echo $form->textFieldRow($model, 'name'); ?>

    <?php echo $form->textFieldRow($model, 'email'); ?>

    <?php echo $form->textFieldRow($model, 'phone'); ?>

    <?php echo $form->dropDownListRow($modelPayment, 'payment_type_id', BOrderPaymentType::model()->listData()); ?>

    <?php echo $form->textAreaRow($model, 'comment'); ?>

    <?php echo $form->dropDownListRow($modelDelivery, 'delivery_type_id', BOrderDeliveryType::model()->listData()); ?>

    <?php echo $form->textFieldRow($modelDelivery, 'address'); ?>

    <?php if( $this->isUpdate() ) { ?>
        <?php $this->renderPartial('orderProducts', CMap::mergeArray($_data_, array('form' => $form)));?>
    <?php }?>

  <?php echo $form->textRow($model, 'sum', array('id' => 'js-order-sum', 'style' => 'font-weight: bolder;'), array('format' => 'price')); ?>

  <?php echo $form->textFieldRow($modelDelivery, 'delivery_price', array('class' => 'span1')); ?>

  <?php echo $form->textRow($model, 'totalSum', array('id' => 'js-total-sum', 'style' => 'font-weight: bolder;'), array('format' => 'price')); ?>

  <?php echo $form->dropDownListRow($model, 'status_id', BOrderStatus::model()->listData('id', 'name', new CDbCriteria(array('order' => 'id')))); ?>

  <?php echo $form->textAreaRow($model, 'order_comment'); ?>

  <tr><td colspan="2" style="text-align: center">
      <div class="s-buttons s-buttons-additional">
        <?php $this->widget('BButton', array(
          'buttonType' => 'submit',
          'type' => 'primary',
          'label' => $model->isNewRecord ? 'Создать' : 'Применить',
          'popupDepended' => false,
        )); ?>

        <?php $this->widget('BButton', array(
          'type' => BButton::BUTTON_LINK,
          'icon' => 'icon-share-alt',
          'label' => 'Отправить уведомление о заказе',
          'url' => $this->createUrl('/order/order/sendNotification', array('orderId' => $model->id))
        ))?>
      </div>
    </td></tr>
  </tbody>
</table>

<?php if( $this->isUpdate() ) { ?>
  <?php $this->renderPartial('orderHistory', array('model' => $model));?>
  <?php $this->renderPartial('_payments', array('model' => $model));?>
<?php } ?>

<?php $this->renderPartial('_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>