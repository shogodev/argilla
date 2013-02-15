<?php
/* @var $this BUserController*/
/* @var $model BFrontendUser */
/* @var $userExtendedData BUserDataExtended*/
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
  <?php echo $form->passwordFieldRow($model, 'password'); ?>
  <?php echo $form->passwordFieldRow($model, 'password_confirm'); ?>
  <?php echo $form->textFieldRow($model, 'email'); ?>

  <?php echo $form->textFieldRow($userExtendedData, 'name'); ?>
  <?php echo $form->textFieldRow($userExtendedData, 'last_name'); ?>
  <?php echo $form->textFieldRow($userExtendedData, 'patronymic'); ?>
  <?php echo $form->textFieldRow($userExtendedData, 'address'); ?>
  <?php echo $form->textFieldRow($userExtendedData, 'birthday'); ?>

  <?php echo $form->uploadRow($userExtendedData, 'avatar', false); ?>

  <?php echo $form->coordinatesRow($userExtendedData, 'coordinates');?>

  <?php echo $form->checkBoxRow($model, 'visible');?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>