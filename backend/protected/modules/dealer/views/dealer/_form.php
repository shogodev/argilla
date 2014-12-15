<?php
/**
 * @var BDealerController $this
 * @var BActiveRecord $models
 * @var BDealer $model
 * @var BFrontendUser $user
 */
?>
<?php Yii::app()->breadcrumbs->show();?>

<?php
/* @var $form BActiveForm */
$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));
?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model)); ?>
<?php echo $form->errorSummary($models); ?>
<?php echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
<tbody>

 <?php if( $user = Arr::get($models, 1) ) $this->renderPartial('user.views.frontendUser._form_user', array('model' => $user, 'form' => $form))?>

  <?php echo $form->textFieldRow($model, 'name'); ?>

  <?php echo $form->uploadRow($model, 'img', false); ?>

  <?php echo $form->textFieldRow($model, 'phone'); ?>

  <?php echo $form->textFieldRow($model, 'person'); ?>

  <?php echo $form->checkBoxRow($model, 'visible');?>

  <?php $this->widget('FilialWidget', array(
   'modelName' => 'BDealerFilial',
   'modelAttribute' => 'dealer_id',
   'modelId' => $model->id,
   'urlRoute' => 'dealerFilial'
  ))?>
</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>