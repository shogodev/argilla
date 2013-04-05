<?php
/* @var BHintController $this */
/* @var BHint $model */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'columns' => array(
    array('name' => 'id', 'htmlOptions' => array('class' => 'center span1'), 'filter' => false),
    array('name' => 'model', 'htmlOptions' => array('class' => 'span3')),
    array('name' => 'attribute', 'htmlOptions' => array('class' => 'span3')),
    array('name' => 'content', 'type' => 'html', 'filter' => false),

    array('class' => 'BButtonColumn'),
  ),
));