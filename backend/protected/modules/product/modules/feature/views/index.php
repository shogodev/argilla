<?php
/**
 * @var BFeatureController $this
 * @var BFeature $model
 * @var BActiveDataProvider $dataProvider
 */

Yii::app()->breadcrumbs->show();
?>
<?php
$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'position', 'class' => 'OnFlyEditField', 'filter' => false),
    array('name' => 'name'),
    array('name' => 'notice', 'filter' => false),
    array('class' => 'BButtonColumn'),
  ),
));?>