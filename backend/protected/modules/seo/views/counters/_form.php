<?php
/* @var BCountersController $this */
/* @var BCounters $model  */
/* @var BActiveForm $form */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId())); ?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php echo $form->renderRequire(); ?>
<?php echo $form->errorSummary($model); ?>

<table class="detail-view table table-striped table-bordered">
  <tbody>

    <?php echo $form->textFieldRow($model, 'name'); ?>

    <?php echo $form->textAreaRow($model, 'code', array('rows' => '10'));?>

    <?php echo $form->checkBoxRow($model, 'main');?>

    <?php echo $form->checkBoxRow($model, 'visible');?>

  </tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>