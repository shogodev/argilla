<?php
/**
 * @var BContactController $this
 * @var BContactTextBlock $model
 * @var $dataProvider BActiveDataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'name', 'htmlOptions' => array('class' => 'center span1')),
    array('name' => 'sysname', 'htmlOptions' => array('class' => 'center span1')),
    array('name' => 'contact_id', 'value' => 'BContact::model()->findByPk($data->contact_id)->name',  'htmlOptions' => array('class' => 'center span1')),

    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
));