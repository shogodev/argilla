<?php
/**
 * @var $this GalleryController
 * @var $model BGallery
 * @var $form BActiveForm
 */

Yii::app()->breadcrumbs->show();

$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));

$this->renderPartial('//_form_buttons', array('model' => $model));
echo $form->errorSummary($model);
echo $form->renderRequire();
?>

<table id="yw0" class="detail-view table table-striped table-bordered">
<thead>
  <th colspan="2">Галерея</th>
</thead>
<tbody>
  <?php echo $form->textFieldRow($model, 'name'); ?>
  <?php echo $form->urlRow($model, 'url'); ?>
  <?php echo $form->textFieldRow($model, 'type');?>
  <?php echo $form->uploadRow($model, 'gallery_image'); ?>
</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>