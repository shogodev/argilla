<?php
/**
 * @var $this BProductFeaturesController
 * @var $dataProvider BActiveDataProvider
 */

Yii::app()->breadcrumbs->show();
?>
<?php
$this->widget('BGridView', array(
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'position', 'class' => 'OnFlyEditField', 'htmlOptions' => array('class' => 'span1'), 'header' => 'Позиция'),
    array('name' => 'name'),
    array('name' => 'notice'),
    array('class' => 'BButtonColumn'),
  ),
));?>