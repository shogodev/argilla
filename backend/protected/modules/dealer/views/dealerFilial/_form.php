<?php
/**
 * @var BDealerFilialController $this
 * @var BDealerFilial $model
 */
?>
<?php Yii::app()->breadcrumbs->show();?>

<?php $form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId())); ?>

<?php
/* @var $form BActiveForm */
$this->renderPartial('//_form_buttons', array('model' => $model));
?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
<tbody>

  <?php echo $form->textFieldRow($model, 'position'); ?>

  <?php echo $form->textFieldRow($model, 'name'); ?>

  <?php echo $form->dropDownListDefaultRow($model, 'city_id', BDealerCity::model()->listData('id', 'name')); ?>

  <?php echo $form->textFieldRow($model, 'address'); ?>

  <?php echo $form->textFieldRow($model, 'worktime'); ?>

  <?php echo $form->textFieldRow($model, 'phone'); ?>

  <?php echo $form->textFieldRow($model, 'phone_additional'); ?>

  <?php echo $form->textFieldRow($model, 'fax'); ?>

  <?php echo $form->textFieldRow($model, 'email'); ?>

  <?php echo $form->textFieldRow($model, 'skype'); ?>

  <?php echo $form->textFieldRow($model, 'site_url'); ?>

  <?php echo $form->coordinatesRow($model, 'coordinates');?>

  <?php echo $form->textAreaRow($model, 'notice'); ?>

  <?php echo $form->checkBoxRow($model, 'visible');?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>