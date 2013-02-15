<?php
/**
 * @var BRedirectController $this
 * @var BRedirect $model
 * @var BActiveForm $form
*/
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId())); ?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
  <tbody>
    <?php echo $form->textFieldRow($model, 'base');?>
    <?php echo $form->textFieldRow($model, 'target');?>
    <?php echo $form->dropDownListDefaultRow($model, 'type_id', BRedirectType::getList());?>
    <?php echo $form->checkBoxRow($model, 'visible');?>
  </tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>