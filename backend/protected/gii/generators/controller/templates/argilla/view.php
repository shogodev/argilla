<?php
/**
 * This is the template for generating an action view file.
 * The following variables are available in this template:
 * - $this: the ControllerCode object
 * - $action: the action ID
 */
?>
<?php echo "<?php\r\n"; ?>
/**
* @var <?php echo $this->getControllerClass(); ?> $this
* @var <?php echo str_replace("Controller", "", $this->getControllerClass()); ?> $model
*/
<?php
if( $action === 'index' ) { ?>
Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'position', 'htmlOptions' => array('class' => 'span1'), 'class' => 'OnFlyEditField', 'filter' => false),
    array('name' => 'name', 'class' => 'BEditColumn'),
    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),
    array('class' => 'BButtonColumn'),
  ),
));<?php } else {?>
<?php echo "?>\r\n"; ?>

<?php echo "<?php ";?>Yii::app()->breadcrumbs->show(); <?php echo "?>\r\n"; ?>

<?php echo "<?php\r\n"; ?>
/* @var $form BActiveForm */
$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));
<?php echo "?>\r\n"; ?>

<?php echo "<?php "; ?>$this->renderPartial('//_form_buttons', array('model' => $model)); ?>
<?php echo "<?php "; ?>echo $form->errorSummary($model); ?>
<?php echo "<?php "; ?>echo $form->renderRequire(); ?>

<table class="detail-view table table-striped table-bordered">
  <tbody>

  <?php echo "<?php "; ?>echo $form->textFieldRow($model, 'position', array('class' => 'span1')); ?>

  <?php echo "<?php "; ?>echo $form->textFieldRow($model, 'name'); ?>

  </tbody>
</table>

<?php echo "<?php "; ?>$this->renderPartial('//_form_buttons', array('model' => $model)); ?>
<?php echo "<?php "; ?>$this->endWidget(); ?>

<?php } ?>