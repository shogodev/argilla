<?php
/* @var $this BOrderController|BController */
/* @var $model BOrder */
/* @var $form CActiveForm|BActiveForm */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId())); ?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model)); ?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
  <tbody>

    <?php echo $form->dateTextRow($model, 'date_create'); ?>

    <?php echo $form->textFieldRow($model, 'name'); ?>

    <?php echo $form->textFieldRow($model, 'email'); ?>

    <?php echo $form->textFieldRow($model, 'phone'); ?>

    <?php echo $form->textFieldRow($model, 'address'); ?>

    <?php echo $form->dropDownListRow($model, 'payment_id', BOrderPaymentType::model()->listData()); ?>

    <?php echo $form->dropDownListRow($model, 'delivery_id', BOrderDeliveryType::model()->listData()); ?>

    <?php echo $form->textAreaRow($model, 'comment'); ?>

    <?php echo $form->textFieldRow($model, 'sum'); ?>

    <?php echo $form->dropDownListRow($model, 'status_id', BOrderStatus::model()->listData()); ?>

    <?php echo $form->textAreaRow($model, 'order_comment'); ?>

  </tbody>
</table>

<?php if( $this->isUpdate() ) { ?>
  <?php $this->renderPartial('orderProducts', array('model' => $model));?>
  <?php $this->renderPartial('orderHistory', array('model' => $model));?>
  <?php $this->renderPartial('_payments', array('model' => $model));?>
<?php } ?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>