<?php
/* @var $this UserController */
/* @var $model User */
/* @var $form BActiveForm */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId())); ?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
<tbody>

  <?php echo $form->textFieldRow($model, 'username'); ?>
  <?php echo $form->passwordFieldRow($model, 'passwordNew'); ?>

  <?php if( !$model->isNewRecord ):?>
  <?php echo $form->checkBoxListRow($model, 'roles', $roles)?>
  <?php endif;?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>