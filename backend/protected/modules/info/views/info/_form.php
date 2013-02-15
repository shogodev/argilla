<?php
/* @var $this BInfoController */
/* @var $model BInfo */
/* @var $path string */
?>

<?php Yii::app()->breadcrumbs->show();?>

<?php
/**
 * @var BActiveForm $form
 */
$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId())); ?>

<?php $this->renderPartial('_form_buttons', array('model' => $model));?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire();?>

<table id="yw0" class="detail-view table table-striped table-bordered">
<thead>
  <th colspan="2">Информационная страница</th>
</thead>
<tbody>

  <tr>
    <td colspan="2"><?php echo $path?> &nbsp;</td>
  </tr>

  <?php echo $form->datePickerRow($model, 'date'); ?>

  <?php echo $form->textFieldRow($model, 'position', array('class' => 'span1')); ?>

  <?php echo $form->textFieldRow($model, 'template', array('maxlength' => 255)); ?>

  <?php echo $form->textFieldRow($model, 'name'); ?>

  <?php echo $form->textFieldRow($model, 'url', array('rel' => 'extender', 'data-extender' => 'translit', 'data-source' => 'input[name*="name"]')); ?>

  <?php echo $form->ckeditorRow($model, 'notice'); ?>

  <?php echo $form->uploadRow($model, 'info_files'); ?>

  <?php echo $form->ckeditorRow($model, 'content'); ?>

  <?php echo $form->textFieldRow($model, 'reference', array('rel' => 'extender', 'data-extender' => 'link')); ?>

  <?php echo $form->checkBoxRow($model, 'visible'); ?>

  <?php echo $form->checkBoxRow($model, 'siblings'); ?>

  <?php echo $form->checkBoxRow($model, 'children'); ?>

  <?php echo $form->checkBoxRow($model, 'menu'); ?>

  <?php echo $form->checkBoxRow($model, 'sitemap'); ?>

</tbody>
</table>

<?php $this->renderPartial('_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>