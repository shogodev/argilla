<?php
/**
 * This is the template for generating an action view file.
 * The following variables are available in this template:
 * - $this: the ControllerCode object
 */
?>
<?php echo "<?php\r\n"; ?>
/**
<?php if( isset($this->controller, $this->modelName ) ) {?>
 * @var <?php echo $this->controller ?> $this
 * @var <?php echo $this->modelName ?> $model
<?php }?>
 * @var BActiveForm $form
*/
<?php echo "?>\r\n"; ?>

<?php echo "<?php ";?>Yii::app()->breadcrumbs->show(); <?php echo "?>\r\n"; ?>

<?php echo "<?php\r\n"; ?>
$form = $this->beginWidget('BActiveForm', array('id' => $model->getFormId()));
<?php echo "?>\r\n"; ?>

<?php echo "<?php "; ?>$this->renderPartial('//_form_buttons', array('model' => $model)); ?>
<?php echo "<?php "; ?>echo $form->errorSummary($model); ?>
<?php echo "<?php "; ?>echo $form->renderRequire(); ?>

  <table class="detail-view table table-striped table-bordered">
  <tbody>

<?php
if( $this->getModelClass() ) {
  foreach($this->getModelAttributes() as $attribute) {
    switch($attribute)
    {
      case 'position':
        echo "    <?php echo \$form->textFieldRow(\$model, '".$attribute."', array('class' => 'span1')); ?>\r\n\r\n";
      break;
      case 'visible':
        echo "    <?php echo \$form->checkBoxRow(\$model, '".$attribute."'); ?>\r\n\r\n";
        break;
      case 'img':
        echo "    <?php echo \$form->uploadRow(\$model, '".$attribute."', false); ?>\r\n\r\n";
        break;
      case 'url':
        echo "    <?php echo \$form->urlRow(\$model, '".$attribute."', false); ?>\r\n\r\n";
        break;
      default:
        /**
         * @var BActiveRecord $model
         */
        $modelName = $this->getModelClass();
        $model = new $modelName;
        $table = $model->dbConnection->getSchema()->getTable($model->tableName());
        /**
         * @var CMysqlColumnSchema $column
         */
        $column = $table->columns[$attribute];
        if( $column->dbType == 'text')
        {
          //echo "    <?php echo \$form->ckeditorRow(\$model, '".$attribute."'); ?//>\r\n\r\n";
          echo "    <?php echo \$form->textAreaRow(\$model, '".$attribute."'); ?>\r\n\r\n";
        }
        else if( $column->dbType == 'int(10) unsigned')
        {
         // '<?php echo \$form->dropDownListDefaultRow($model, '".$attribute."', RelatedModel::model()->listData('id', 'name')); ?//>'
          echo "    <?php echo \$form->textFieldRow(\$model, '".$attribute."'); ?>\r\n\r\n";
        }
        else if( $column->dbType == 'tinyint(1)')
        {
          echo "    <?php echo \$form->checkBoxRow(\$model, '".$attribute."'); ?>\r\n\r\n";
        }
        else if( $column->dbType == 'timestamp')
        {
          echo "    <?php if( \$this->isUpdate() ) echo \$form->dateTextRow(\$model, '".$attribute."'); ?>\r\n\r\n";
        }
        else
        {
          echo "    <?php echo \$form->textFieldRow(\$model, '".$attribute."'); ?>\r\n\r\n";
        }
      break;
    }
  }
}
?>
  </tbody>
</table>

<?php echo "<?php "; ?>$this->renderPartial('//_form_buttons', array('model' => $model)); ?>
<?php echo "<?php "; ?>$this->endWidget(); ?>