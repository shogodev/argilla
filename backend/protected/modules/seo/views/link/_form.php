<?php
/**
 * @var BLinkController $this
 * @var BLink $model
 * @var BActiveForm $form
 */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
<tbody>

  <?php echo $form->textFieldRow($model, 'position', array('class' => 'span1')); ?>

  <?php echo $form->textFieldRow($model, 'page'); ?>

  <?php echo $form->dropDownListRow($model, 'section_id', CHtml::listData(BLinkSection::model()->findAll(), 'id', 'name')); ?>

  <?php echo $form->datePickerRow($model, 'date'); ?>

  <?php echo $form->textFieldRow($model, 'url'); ?>

  <?php echo $form->textFieldRow($model, 'title'); ?>

  <?php echo $form->ckeditorRow($model, 'content');?>

  <?php echo $form->textFieldRow($model, 'region'); ?>

  <?php echo $form->textFieldRow($model, 'email'); ?>

  <?php echo $form->checkBoxRow($model, 'visible');?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>