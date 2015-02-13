<?php
/**
 * This is the template for generating an action view file.
 * The following variables are available in this template:
 * - $this: the ControllerCode object
 */
?>
<?php echo "<?php\n"; ?>
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
  foreach($this->getModelAttributes() as $attribute) {
    switch($attribute)
    {
      case 'position':
        echo "    array('name' => '".$attribute."', 'htmlOptions' => array('class' => 'span1'), 'class' => 'OnFlyEditField', 'filter' => false),\n";
        break;
      case 'id':
        echo "    array('name' => '".$attribute."', 'class' => 'BPkColumn', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),\n";
        break;
      case 'name':
      case 'notice':
        echo "    array('name' => '".$attribute."'),\n";
        break;
      case 'visible':
        echo "    array('name' => '".$attribute."', 'class' => 'JToggleColumn', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),\n";
        break;
      default:
        if( count($this->getModelAttributes()) < 7 )
          echo "    array('name' => '".$attribute."'),\n";
        break;
    }
  }
}?>
      array('class' => 'BButtonColumn'),
    ),
  ));