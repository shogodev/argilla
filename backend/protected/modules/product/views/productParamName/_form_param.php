<?php
/* @var BProductParamNameController $this */
/* @var BProductParamName $model */
/* @var BActiveForm $form */
?>

<?php echo $form->dropDownListRow($model, 'parent', CHtml::listData(BProductParamName::model()->groups()->findAll(), 'id', 'name')); ?>
<?php echo $form->textFieldRow($model, 'position', array('class' => 'span1')); ?>

<?php echo $form->textFieldRow($model, 'name'); ?>

<?php echo $form->dropDownListRow($model, 'type', $model->types); ?>

<?php if( !in_array($model->type, array('text', 'slider')) ) { ?>
  <?php echo $form->relatedItemsRow($model, 'variants', array(
    'position' => array('class' => 'span1'),
    'name',
    'notice' => $model->key == 'color' ? array('label'=> 'Цвет') : null
  )) ?>
<?php } ?>

<?php echo $form->textFieldRow($model, 'key', array('class' => 'span4'));?>

<?php echo $form->checkBoxRow($model, 'visible');?>

<?php echo $form->checkBoxRow($model, 'product');?>

<?php echo $form->checkBoxRow($model, 'section');?>

<?php echo $form->checkBoxRow($model, 'section_list');?>

<?php echo $form->checkBoxRow($model, 'selection');?>