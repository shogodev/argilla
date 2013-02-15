<?php
/* @var BResponseController $this */
/* @var BResponse $model */
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

  <tr>
    <th><label>Продукт</label></th>
    <td><div><?php echo $model->product->name?></div></td>
  </tr>

  <?php echo $form->textFieldRow($model, 'name'); ?>

  <?php echo $form->textFieldRow($model, 'email'); ?>

  <?php echo $form->ckeditorRow($model, 'content');?>

  <?php echo $form->checkBoxRow($model, 'visible');?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>