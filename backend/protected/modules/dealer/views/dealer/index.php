<?php
/**
 * @var BDealerController $this
 * @var BActiveDataProvider $dataProvider
 */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $dataProvider,
  'columns'      => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'name'),
    array('name' => 'phone'),
    array('name' => 'person'),
    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),
    array('class' => 'BButtonColumn'),
  ),
));