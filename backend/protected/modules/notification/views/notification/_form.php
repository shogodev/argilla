<?php
/* @var $this BNotificationController|BController */
/* @var $form CActiveForm|BActiveForm */
/* @var $model BNotification */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId())); ?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model)); ?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
<tbody>

  <?php if( !$this->isUpdate() ) { ?>
    <?php echo $form->textFieldRow($model, 'index'); ?>
  <?php } else {?>
    <?php echo $form->textRow($model, 'index') ?>
  <?php }?>

  <?php echo $form->textFieldRow($model, 'name'); ?>

  <?php echo $form->textFieldRow($model, 'email'); ?>

  <?php echo $form->textFieldRow($model, 'subject'); ?>

  <?php echo $form->dropDownListDefaultRow($model, 'view', CHtml::listData($model->getViews(),'id', 'name')); ?>

  <?php echo $form->textAreaRow($model, 'message'); ?>

  <?php echo $form->checkBoxRow($model, 'visible');?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>