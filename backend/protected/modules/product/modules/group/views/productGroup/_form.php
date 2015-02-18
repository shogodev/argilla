<?php
/**
 * @var BProductGroupController $this
 * @var BProductGroup $model
 */
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

  </tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>