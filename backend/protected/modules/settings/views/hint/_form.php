<?php
/* @var BHintController $this */
/* @var BHint $model */
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

  <?php echo $form->textFieldRow($model, 'model'); ?>

  <?php echo $form->textFieldRow($model, 'attribute'); ?>

  <?php echo $form->textAreaRow($model, 'content'); ?>

  <?php echo $form->checkBoxRow($model, 'popup'); ?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>