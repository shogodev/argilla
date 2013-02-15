<?php
/* @var BProductParamNameController $this */
/* @var BActiveDataProvider $dataProvider */
/* @var BProductParamName $model */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'filter' => $model,
  'dataProvider' => $model->search(),
  'buttonsTemplate' => '_form_button_create',
  'rowCssClassExpression' => '$data->isGroup() ? "group" : ($row % 2 ? "odd" : "even" )',
  'columns' => array(
    array('name' => 'position', 'htmlOptions' => array('class' => 'span1'), 'class' => 'OnFlyEditField', 'header' => 'Позиция'),
    array('name' => 'name', 'header' => 'Название', 'filter' => false),

    array(
      'name' => 'section_id',
      'filter' => CHtml::listData(BProductSection::model()->findAll(), 'id', 'name'),
      'hideColumn' => true,
    ),

    array('name' => 'type', 'header' => 'Тип', 'filter' => false, 'value' => '$data->isGroup() ? "" : $data->types[$data->type]'),

    array('class' => 'ParamToggleColumn', 'name' => 'visible', 'header' => 'Вид'),
    array('class' => 'ParamToggleColumn', 'name' => 'product', 'header' => 'Товар'),
    array('class' => 'ParamToggleColumn', 'name' => 'section', 'header' => 'Разводная'),

    array('class' => 'ParamButtons'),
  ),
));