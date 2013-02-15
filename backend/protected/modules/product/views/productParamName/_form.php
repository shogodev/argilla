<?php
/* @var BProductParamNameController $this */
/* @var BProductParamName $model */
/* @var BProductParamAssignment $assignmentModel */
/* @var array $_data_ */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php
/* @var $form BActiveForm */
$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));
?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
<tbody>
  <?php $this->renderPartial('_form_'.($model->isGroup() ? 'group' : 'param'), CMap::mergeArray($_data_, array('form' => $form))); ?>
</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>