<?php
/**
 * @var $this BContactController
 * @var $dataProvider BActiveDataProvider
 */

 Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'address', 'htmlOptions' => array('class' => 'span1'), 'type' => 'html'),
    array('name' => 'url', 'htmlOptions' => array('class' => 'center span1'), 'type' => 'html'),
    array('name' => 'name', 'htmlOptions' => array('class' => 'center span1'), 'type' => 'html'),

    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
));