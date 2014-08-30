<?php
/**
 * @var $this BMenuCustomItemController
 * @var $form BActiveForm
 * @var $model BFrontendCustomMenuItem
 */
Yii::app()->breadcrumbs->show();

$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));

$this->renderPartial('//_form_buttons', array('model' => $model));
echo $form->errorSummary($model);
echo $form->renderRequire();
?>
  <table class="detail-view table table-striped table-bordered">
    <thead>
    <tr>
      <th colspan="2">Меню</th>
    </tr>
    </thead>
    <tbody>
    <?php echo $form->textFieldRow($model, 'name'); ?>
    <?php echo $form->textFieldRow($model, 'url'); ?>

    <?php if( !$model->isNewRecord ) echo $form->relatedItemsRow($model, 'data', array('name','value',)); ?>
    </tbody>
  </table>
<?php $this->renderPartial('//_form_buttons', array('model' => $model));?>
<?php $this->endWidget(); ?>