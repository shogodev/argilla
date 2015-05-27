<?php
/**
 * @var BOrderProductItemController $this
 * @var BActiveDataProvider $dataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'dataProvider' => $dataProvider,
  'columns'      => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'name', 'value' => '$data->variant->name' ),
  ),
));