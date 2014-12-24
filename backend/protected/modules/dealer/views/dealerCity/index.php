<?php
/**
 * @var BDealerCityController $this
 * @var BActiveDataProvider $dataProvider
 */
Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns'      => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'position', 'htmlOptions' => array('class' => 'span1'), 'class' => 'OnFlyEditField'),
    array('name' => 'name'),
    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),
    array('class' => 'BButtonUpdateColumn'),
  ),
));