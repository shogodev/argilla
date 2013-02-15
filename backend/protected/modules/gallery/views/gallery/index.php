<?php
/**
 * @var $this BGalleryController
 * @var $model BGallery
 * @var $dataProvider BActiveDataProvider
 * @var $model BGallery
 */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'dataProvider' => $model->search(),
  'filter' => $model,
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'name', 'htmlOptions' => array('class' => 'center')),
    array('name' => 'url', 'htmlOptions' => array('class' => 'center')),
    array('name' => 'type', 'htmlOptions' => array('class' => 'center')),

    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),
    array('class' => 'BButtonColumn'),
  ),
));
