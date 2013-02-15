<?php
/* @var BTextBlockController $this */
/* @var BTextBlock $model */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php
/* @var $form BActiveForm */
$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));
?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
<tbody>

  <?php echo $form->textFieldRow($model, 'position', array('class' => 'span1')); ?>

  <?php echo $form->autocompleteRow($model, 'location'); ?>

  <?php echo $form->textFieldRow($model, 'name'); ?>

  <?php echo $form->textFieldRow($model, 'url'); ?>

  <?php echo $form->uploadRow($model, 'img', false)?>

  <?php echo $form->ckeditorRow($model, 'content');?>

  <?php echo $form->checkBoxRow($model, 'visible');?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>