<?php
/**
 * This is the template for generating an action view file.
 * The following variables are available in this template:
 * - $this: the ControllerCode object
 */
?>
<?php echo "<?php\r\n"; ?>
<?php if( isset($this->controller, $this->modelName ) ) {?>
  /**
  * @var <?php echo $this->controller ?> $this
  * @var <?php echo $this->modelName ?> $model
  */
<?php }?>
  Yii::app()->breadcrumbs->show();

  $this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns' => array(
<?php
if( $this->getModelClass() ) {
  /**
   * @var BActiveRecord $model
   */
  $modelName = $this->getModelClass();
  $model = new $modelName;
  $table = $model->dbConnection->getSchema()->getTable($model->tableName());

  foreach($this->getModelAttributes() as $key => $attribute) {
    switch($attribute)
    {
      case 'position':
        echo "      array('name' => '".$attribute."', 'htmlOptions' => array('class' => 'span1'), 'class' => 'OnFlyEditField', 'filter' => false),\r\n";
        break;
      case 'id':
        echo "      array('name' => '".$attribute."', 'class' => 'BPkColumn', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),\r\n";
        break;
      case 'name':
        echo "      array('name' => '".$attribute."', 'htmlOptions' => array('class' => 'span6'), 'class' => 'OnFlyEditField'),\r\n";
        break;
      case 'notice':
        echo "      array('name' => '".$attribute."'),\r\n";
        break;
      case 'visible':
        echo "      array('name' => '".$attribute."', 'class' => 'JToggleColumn', 'filter' => CHtml::listData(\$model->yesNoList(), 'id', 'name')),\r\n";
        break;
      default:
        if( ($key + 1) < 7 )
        {
          /**
           * @var CMysqlColumnSchema $column
           */
          $column = $table->columns[$attribute];
          if( $column->dbType == 'timestamp' )
            echo "      array('name' => '".$attribute."', 'class' => 'BDatePickerColumn'),\r\n";
          else
            echo "      array('name' => '".$attribute."'),\r\n";
          break;
        }
    }
  }
}?>
      array('class' => 'BButtonColumn'),
    ),
  ));