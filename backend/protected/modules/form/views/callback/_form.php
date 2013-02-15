<?php
/* @var BCallbackController $this */
/* @var BCallback $model */
/* @var BActiveForm $form */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php
/**
 * @var $form BActiveForm
 */
$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId())); ?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php echo $form->renderRequire(); ?>
<?php echo $form->errorSummary($model); ?>

<table class="detail-view table table-striped table-bordered">
  <tbody>

  <?php echo $form->dateTextRow($model, 'date'); ?>
  <?php echo $form->textFieldRow($model, 'name'); ?>
  <?php echo $form->textFieldRow($model, 'phone');?>
  <?php echo $form->textFieldRow($model, 'time');?>
  <?php echo $form->textAreaRow($model, 'content');?>
  <?php echo $form->textAreaRow($model, 'result');?>

  </tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>
