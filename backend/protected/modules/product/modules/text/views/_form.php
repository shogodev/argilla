<?php
/**
* @var BProductTextController $this
* @var BProductText $model
*/
?>

<?php Yii::app()->breadcrumbs->show(); ?>

<?php
/* @var $form BActiveForm */
$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));
?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model)); ?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
  <tbody>

  <?php echo $form->textFieldRow($model, 'url'); ?>

  <?php echo $form->ckeditorRow($model, 'content_upper'); ?>

  <?php echo $form->ckeditorRow($model, 'content'); ?>

  <?php echo $form->checkBoxRow($model, 'visible');?>

  </tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model)); ?>
<?php $this->endWidget(); ?>

