<?php
/* @var $this BLinkSectionController */
/* @var $model BLinkSection */
/* @var $form BActiveForm */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId())); ?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php echo $form->renderRequire(); ?>
<?php echo $form->errorSummary($model); ?>

<table class="detail-view table table-striped table-bordered">
  <tbody>

  <?php echo $form->textFieldRow($model, 'position', array('class' => 'span1')); ?>

  <?php echo $form->textFieldRow($model, 'name'); ?>

  <?php echo $form->urlRow($model, 'url'); ?>

  <?php echo $form->checkBoxRow($model, 'visible');?>

  </tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>
