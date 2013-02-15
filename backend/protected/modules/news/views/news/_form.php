<?php
/* @var BNewsController $this */
/* @var BNews $model */
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

  <?php echo $form->dropDownListRow($model, 'section_id', CHtml::listData(BNewsSection::model()->findAll(), 'id', 'name')); ?>

  <?php echo $form->datePickerRow($model, 'date'); ?>

  <?php echo $form->textFieldRow($model, 'name'); ?>

  <?php echo $form->urlRow($model, 'url'); ?>

  <?php echo $form->uploadRow($model, 'img', false)?>

  <?php echo $form->ckeditorRow($model, 'notice');?>

  <?php echo $form->ckeditorRow($model, 'content');?>

  <?php echo $form->checkBoxRow($model, 'main');?>

  <?php echo $form->checkBoxRow($model, 'visible');?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>