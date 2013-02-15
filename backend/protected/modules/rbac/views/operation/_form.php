<?php
/* @var $this BRbacOperationController */
/* @var $model BRbacOperation */
/* @var $form BActiveForm */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId())); ?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
<tbody>

  <?php echo $form->textFieldRow($model, 'title'); ?>

  <?php if( $model->isNewRecord ):?>
    <?php echo $form->textFieldRow($model, 'name'); ?>
  <?php else:?>
    <?php echo $form->uneditableRow($model, 'name'); ?>
  <?php endif;?>

  <?php echo $form->textAreaRow($model, 'description'); ?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>