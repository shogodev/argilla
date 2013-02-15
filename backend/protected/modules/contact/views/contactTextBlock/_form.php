<?php
/* @var $this BContactController */
/* @var $model BContact */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php
/* @var $form BActiveForm */
$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId())); ?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
<tbody>

  <?php echo $form->textFieldRow($model, 'name'); ?>

  <?php echo $form->dropDownListRow($model, 'contact_id', $model->contactList)?>

  <?php if( empty($model->sysname) ):?>
    <?php echo $form->textFieldRow($model, 'sysname'); ?>
  <?php else:?>
   <?php echo $form->uneditableRow($model, 'sysname');?>
  <?php endif;?>

  <?php echo $form->textareaRow($model, 'content'); ?>
  <?php echo $form->textFieldRow($model, 'position', array('class' => 'span1')); ?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>