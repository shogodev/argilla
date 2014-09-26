<?php
/* @var BProductCollectionController $this */
/* @var BProductCollection $model */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php
/* @var $form BActiveForm */
$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));
?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php echo $form->errorSummary(array($model)); ?>
<?php echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
<tbody>

  <?php echo $form->textFieldRow($model, 'position', array('class' => 'span1')); ?>

  <?php echo $form->textFieldRow($model, 'name'); ?>

  <?php echo $form->urlRow($model, 'url'); ?>

  <?php echo $form->dropDownListDefaultRow($model, 'parent_id', CHtml::listData($model->getParents(), 'id', 'name'))?>

  <?php echo $form->uploadRow($model, 'img', false); ?>

  <?php echo $form->ckeditorRow($model, 'notice');?>

  <?php echo $form->checkBoxRow($model, 'visible');?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>