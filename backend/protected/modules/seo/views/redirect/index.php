<?php
/**
 * @var BRedirectController $this
 * @var BRedirect $model
 * @var BActiveDataProvider $dataProvider
 */
?>

<?php Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'dataProvider' => $dataProvider,
  'filter' => $model,
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'base', 'header' => 'Исходный URL'),
    array('name' => 'target', 'header' => 'Конечный URL'),

    array('name' => 'type_id', 'value' => 'BRedirectType::getList()[$data->type_id]', 'filter' => BRedirectType::getList()),

    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
));