<?php
/**
 * @var BFrontendUser $model
 * @var BActiveForm $form
 */
?>
<?php
if( $this->isUpdate() )
  echo $form->dateTextRow($model, 'date_create');
?>
<?php echo $form->textFieldRow($model, 'login'); ?>

<?php echo $form->passwordFieldRow($model, 'password', array('autocomplete' => 'off')); ?>

<?php echo $form->passwordFieldRow($model, 'confirmPassword', array('autocomplete' => 'off')); ?>

<?php echo $form->textFieldRow($model, 'email'); ?>