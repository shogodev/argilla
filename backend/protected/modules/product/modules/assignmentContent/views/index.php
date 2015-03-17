<?php
/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @var BProductBannerController $this
 * @var BProductBanner $model
 * @var BActiveDataProvider $dataProvider
 */

Yii::app()->breadcrumbs->show();

$this->widget('BGridView', array(
  'dataProvider' => $model->search(),
  'filter' => $model,
  'columns' => array(
    array('name' => 'id', 'class' => 'BPkColumn', 'filter' => false, 'htmlOptions' => array('class' => 'span1')),
    array('name' => 'section_id', 'value' => '$data->getAssignmentModelName("section")', 'filter' => BProductSection::model()->listData()),
    array('name' => 'type_id', 'value' => '$data->getAssignmentModelName("type")', 'filter' => BProductType::model()->listData()),
    array('name' => 'category_id', 'value' => '$data->getAssignmentModelName("category")', 'filter' => BProductCategory::model()->listData()),
    array('name' => 'collection_id', 'value' => '$data->getAssignmentModelName("collection")', 'filter' => BProductCollection::model()->listData()),
    array('name' => 'location', 'value' => 'AssignmentContentModule::$locations[$data->location]', 'filter' => AssignmentContentModule::$locations),
    array('class' => 'JToggleColumn', 'name' => 'visible', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),
    array('class' => 'BButtonColumn'),
  ),
));