<?php
/* @var BProductSectionController $this */
/* @var BProductSection $model */
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

  <?php echo $form->textFieldRow($model, 'name'); ?>

  <?php echo $form->uploadRow($model, 'img', false);?>

  <?php echo $form->radioButtonListRow($model, 'variant_id', CHtml::listData(BColor::model()->getVariants(), 'id', function(BProductParamVariant $item) {
    return CHtml::tag('div', array('style' => 'display: inline-block; margin-right: 5px;'), CHtml::image($item->getImage(), '', array('style' => 'width: 20px;'))).CHtml::tag('span', array(), $item->name);
  }));?>

</tbody>
</table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>