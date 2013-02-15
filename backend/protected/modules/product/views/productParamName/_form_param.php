<?php
/* @var BProductParamNameController $this */
/* @var BProductParamName $model */
?>

<?php echo $form->textFieldRow($model, 'position', array('class' => 'span1')); ?>

<?php echo $form->textFieldRow($model, 'name'); ?>

<?php echo $form->dropDownListRow($model, 'type', $model->types); ?>

<?php if( !in_array($model->type, array('text', 'slider')) ) { ?>
  <?php echo $form->relatedItemsRow($model, 'variants', array('name')) ?>
<?php } ?>

<?php echo $form->textFieldRow($model, 'key', array('class' => 'span4'));?>

<?php echo $form->textFieldRow($model, 'group', array('class' => 'span1')); ?>

<?php echo $form->checkBoxRow($model, 'visible');?>