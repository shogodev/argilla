<?php
/**
 * @var $this BGalleryController
 * @var $model BGallery
 * @var BActiveDataProvider $dataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn'),
    array('name' => 'name', 'htmlOptions' => array('class' => 'center')),
    array('name' => 'url', 'htmlOptions' => array('class' => 'center')),
    array('name' => 'type', 'htmlOptions' => array('class' => 'center')),

    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
));
