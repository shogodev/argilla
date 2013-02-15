<?php
/* @var $this BBannerController */
/* @var $model BBanner */
/* @var $form BActiveForm */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId())); ?>
<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
<tbody>

  <?php echo $form->textFieldRow($model, 'position', array('class' => 'span1')); ?>
  <?php echo $form->autocompleteRow($model, 'location'); ?>
  <?php echo $form->textFieldRow($model, 'title'); ?>
  <?php echo $form->textFieldRow($model, 'url'); ?>
  <?php echo $form->uploadRow($model, 'img', false); ?>
  <?php echo $form->textFieldRow($model, 'swf_w', array('class' => 'span1')); ?>
  <?php echo $form->textFieldRow($model, 'swf_h', array('class' => 'span1')); ?>
  <?php echo $form->textAreaRow($model, 'code'); ?>
  <?php echo $form->textAreaRow($model, 'pagelist'); ?>
  <?php echo $form->textAreaRow($model, 'pagelist_exc'); ?>
  <?php echo $form->checkBoxRow($model, 'new_window'); ?>
  <?php echo $form->checkBoxRow($model, 'visible'); ?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>