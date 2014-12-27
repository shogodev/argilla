<?php
/**
 * @var BProductColorController $this
 * @var BProductColor $model
 * @var BProductCategory $category
 */
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

  <?php echo $form->dropDownListDefaultRow($model, 'color_id', CHtml::listData(BColor::model()->findAll(), 'id', 'name')); ?>

  <?php echo $form->checkBoxRow($model, 'visible'); ?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>