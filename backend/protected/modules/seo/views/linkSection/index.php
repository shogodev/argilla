<?php
/**
 * @var BLinkSection $model
 * @var BLinkSectionController $this
 * @var BActiveDataProvider $dataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'position', 'header' => 'Позиция', 'htmlOptions' => array('class' => 'span1'), 'class' => 'OnFlyEditField', 'filter' => false),
    array('name' => 'name', 'htmlOptions' => array()),
    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
));