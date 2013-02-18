<?php
/**
* @var BLinkController $this
* @var BLink $model
*/

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'position', 'htmlOptions' => array('class' => 'span1'), 'class' => 'OnFlyEditField', 'filter' => false),
    array('name' => 'url'),
    array('name' => 'section_id', 'value' => '$data->section->name', 'filter' => CHtml::listData(BLinkSection::model()->findAll(), 'id', 'name')),

    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),
    array('class' => 'BButtonColumn'),
  ),
));