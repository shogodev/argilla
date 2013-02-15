<?php
/* @var BNewsSectionController $this */
/* @var BNewsSection $model */
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

  <?php echo $form->textFieldRow($model, 'position', array('class' => 'span1')); ?>

  <?php echo $form->textFieldRow($model, 'name'); ?>

  <?php echo $form->urlRow($model, 'url'); ?>

  <?php echo $form->ckeditorRow($model, 'notice');?>

  <?php echo $form->checkBoxRow($model, 'visible');?>

  </tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>
