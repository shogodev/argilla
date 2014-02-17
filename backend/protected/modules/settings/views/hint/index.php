<?php
/**
 * @var BHint $model
 * @var BHintController $this
 * @var BActiveDataProvider $dataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'model', 'htmlOptions' => array('class' => 'span3')),
    array('name' => 'attribute', 'htmlOptions' => array('class' => 'span3')),
    array('name' => 'content', 'type' => 'html', 'filter' => false),

    array('class' => 'BButtonColumn'),
  ),
));