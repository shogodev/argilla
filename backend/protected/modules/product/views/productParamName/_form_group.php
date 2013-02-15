<?php
/* @var BProductParamNameController $this */
/* @var BProductParamName $model */
/* @var BProductParamAssignment $assignmentModel */
?>

<?php echo $form->textFieldRow($model, 'position', array('class' => 'span1')); ?>

<?php echo $form->textFieldRow($model, 'name'); ?>

<?php echo $form->dropDownListDefaultRow($assignmentModel, 'section_id', CHtml::listData(BProductSection::model()->findAll(), 'id', 'name')); ?>

<?php echo $form->textFieldRow($model, 'key', array('class' => 'span4'));?>

<?php echo $form->checkBoxRow($model, 'visible');?>