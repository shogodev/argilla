<?php
/* @var BMetaRouteController $this */
/* @var BMetaRoute $model */
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

<?php echo $form->dropDownListRow($model, 'route', CHtml::listData($model->getRoutesList(), 'id', 'name'), $model->getRoutesListOptions()); ?>

<tr>
    <th><label class="required">Переменные моделей</label></th>
    <td>
      <?php foreach($model->getModelVariables($model->id) as $key => $value) {?>
        <div>
            <b><?php echo $key?>:</b>
          <?php echo implode(', ', $value) ?>
        </div>
      <?php }?>
    </td>
</tr>

<?php echo $form->textRow($model, 'clips'); ?>
<?php echo $form->textRow($model, 'globalVars'); ?>
<?php echo $form->textFieldRow($model, 'title'); ?>
<?php echo $form->textAreaRow($model, 'description'); ?>
<?php echo $form->textAreaRow($model, 'keywords'); ?>
<?php echo $form->checkBoxRow($model, 'visible'); ?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>