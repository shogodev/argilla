<?php
/* @var BProductController $this */
/* @var array $_data_ */
/* @var BProduct $model */
/* @var BProductAssignment $assignmentModel */
?>

<?php Yii::app()->breadcrumbs->show(); ?>

<?php
/* @var $form BActiveForm */
$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));
?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model)); ?>
<?php echo $form->errorSummary(array($model) + $assignmentModel); ?>
<?php echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
  <tbody>

  <?php echo $form->textFieldRow($model, 'position', array('class' => 'span1')); ?>

  <?php echo $form->textFieldRow($model, 'name'); ?>

  <?php echo $form->urlRow($model, 'url'); ?>

  <?php echo $form->textFieldRow($model, 'articul'); ?>

  <?php echo $form->dependedInputs($model, 'section_id', array('type_id'), CHtml::listData(BProductSection::model()->findAll(), 'id', 'name')); ?>

  <?php echo $form->textFieldRow($model, 'price', array('class' => 'span4')); ?>

  <?php echo $form->uploadRow($model, 'product_img')?>

  <?php echo $form->ckeditorRow($model, 'content');?>

  <?php echo $form->ckeditorRow($model, 'notice');?>

  <?php echo $form->checkBoxRow($model, 'visible');?>

  </tbody>
</table>

<?php $this->renderPartial('params', CMap::mergeArray($_data_, array('form' => $form))); ?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model)); ?>
<?php $this->endWidget(); ?>