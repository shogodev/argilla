<?php
/* @var $this BUserController*/
/* @var $model BFrontendUser */
/* @var $userProfile BUserProfile*/
/* @var $form CActiveForm|BActiveForm */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php $form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId())); ?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model)); ?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
<tbody>

  <?php
    if( $this->isUpdate() )
     echo $form->dateTextRow($model, 'date_create');
  ?>
  <?php echo $form->textFieldRow($model, 'login'); ?>
  <?php echo $form->passwordFieldRow($model, 'password', array('autocomplete' => 'off')); ?>
  <?php echo $form->passwordFieldRow($model, 'confirmPassword', array('autocomplete' => 'off')); ?>
  <?php echo $form->textFieldRow($model, 'email'); ?>

  <?php echo $form->textFieldRow($userProfile, 'name'); ?>
  <?php echo $form->textFieldRow($userProfile, 'last_name'); ?>
  <?php echo $form->textFieldRow($userProfile, 'patronymic'); ?>
  <?php echo $form->textFieldRow($userProfile, 'phone'); ?>
  <?php echo $form->textFieldRow($userProfile, 'address'); ?>
  <?php echo $form->textFieldRow($userProfile, 'birthday'); ?>

  <?php echo $form->checkBoxRow($model, 'visible');?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>