<?php
/**
 * @var BSitemapController $this
 * @var BSitemapRoute      $model
 * @var BActiveForm       $form
 */
?>
<?php $form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId())); ?>

<?php $this->renderPartial('//_form_buttons', array('model' => $model)); ?>
<?php echo $form->errorSummary($model); ?>
<?php echo $form->renderRequire(); ?>

  <table class="detail-view table table-striped table-bordered">
    <tbody>

      <?php echo $form->dropDownListRow($model, 'route', $model->getRoutes(require_once(Yii::getPathOfAlias('frontend.config').'/routes.php')), array('class' => 'bb')); ?>
      <?php echo $form->dropDownListRow($model, 'changefreq', $model->changeFreqs, array('class' => 'bb')); ?>
      <?php echo $form->textFieldRow($model, 'priority'); ?>
      <?php echo $form->checkBoxRow($model, 'lastmod'); ?>
      <?php echo $form->checkBoxRow($model, 'visible'); ?>

    </tbody>
  </table>

<?php $this->renderPartial('//_form_buttons', array('model' => $model)); ?>
<?php $this->endWidget(); ?>