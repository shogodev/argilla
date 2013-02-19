<?php
/**
 * @var $this BContactController
 * @var $dataProvider BActiveDataProvider
 */

 Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'dataProvider' => $dataProvider,
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'name'),
    array('name' => 'sysname', 'htmlOptions' => array('class' => 'span3')),
    array('name' => 'url', 'htmlOptions' => array('class' => 'span3')),


    array('class' => 'JToggleColumn', 'name' => 'visible'),
    array('class' => 'BButtonColumn'),
  ),
));