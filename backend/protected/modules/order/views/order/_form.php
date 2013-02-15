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
  <?php echo $form->textAreaRow($model, 'comment'); ?>
  <?php echo $form->textFieldRow($model, 'sum'); ?>


<?php if( $this->isUpdate() ) {?>
  <tr><th><label>Статус</label></th><td>
    <div class="m10"><?php echo CHtml::tag('b', array(), $model->statusLabel[$model->status])?></div>
    <?php if( $model->status == BOrder::STATUS_NEW) { ?>
      <div class="m10"><?php echo CHtml::label('Комментарий/причина отмены:', 'order_comment')?>
      <?php echo CHtml::textArea('order_comment')?></div>
      <?php $model->renderButtonConfirm($this) ?>
      <?php $model->renderButtonCancel($this) ?>
    <?php } else { ?>
    <?php echo CHtml::label('Комментарий/причина отмены:', 'order_comment')?>
    <?php echo CHtml::textArea('order_comment', $model->order_comment, array('readonly' => 'readonly'))?>
    <?php } ?>
  </td></tr>
<?php } ?>

</tbody>
</table>

<?php if( $this->isUpdate() )
        $this->renderPartial('orderProducts', array('model' => $model)); ?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>