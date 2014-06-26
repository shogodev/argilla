<?php
/* @var BGridSettingsController $this */
/* @var BGridSettings $model */
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

  <?php echo $form->textFieldRow($model, 'name'); ?>

  <?php echo $form->textFieldRow($model, 'header'); ?>

  <?php echo $form->dropDownListDefaultRow($model, 'class', $model->getClasses()); ?>

  <?php echo $form->dropDownListDefaultRow($model, 'type', $model->getTypes()); ?>

  <?php echo $form->dropDownListDefaultRow($model, 'filter', BGridSettings::$labels); ?>

  <?php echo $form->checkBoxRow($model, 'visible'); ?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>