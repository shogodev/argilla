<?php
/**
 * @var BTagController $this
 * @var BTag $model
 * @var BActiveForm $form
*/
?>

<?php Yii::app()->breadcrumbs->show(); ?>

<?php
$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));
?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model)); ?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire(); ?>

  <table class="detail-view table table-striped table-bordered">
  <tbody>

    <?php echo $form->textFieldRow($model, 'name'); ?>

    <?php echo $form->dropDownListDefaultRow($model, 'group', TagModule::$tagGroupList); ?>

  </tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model)); ?>
<?php $this->endWidget(); ?>