<?php
  /**
  * @var BOrderDeliveryTypeController $this
  * @var BOrderDeliveryType $model
  */
  Yii::app()->breadcrumbs->show();

  $this->widget('BGridView', array(
    'template' => "{summary}\n{items}\n{pagesize}\n{pager}\n{scripts}",
    'filter' => $model,
    'dataProvider' => $model->search(),
    'columns' => array(
        array('name' => 'position', 'header' => 'Позиция', 'htmlOptions' => array('class' => 'span1'), 'class' => 'OnFlyEditField', 'filter' => false),
        array('name' => 'name', 'header' => 'Название', 'htmlOptions' => array('class' => 'span6'), 'class' => 'OnFlyEditField', 'filter' => false),
        array('name' => 'minimal_price', 'header' => 'Мин. стоимость заказа', 'class' => 'OnFlyEditField', 'filter' => false),
        array(
          'name' => 'price',
          'header' => 'Стоимость',
          'class' => 'BConditionDataColumn',
          'filter' => false,
          'htmlOptions' => array('class' => 'span2'),
          'columns' => array(
            array('class' => 'BDataColumn', 'value' => '"бесплатно"'),
            array('class' => 'OnFlyEditField'),
          ),
          'condition' => '!empty($data["always_free_delivery"]) ? 0 : 1;'
        ),
        array(
          'name' => 'free_delivery_price_limit',
          'header' => 'Лимит беспл. доставки',
          'class' => 'BConditionDataColumn',
          'filter' => false,
          'htmlOptions' => array('class' => 'span2'),
          'columns' => array(
             array('class' => 'BDataColumn', 'value' => '"любой"'),
             array('class' => 'OnFlyEditField'),
          ),
          'condition' => '!empty($data["always_free_delivery"]) ? 0 : 1;'
        ),
        array(
          'name' => 'always_free_delivery',
          'class' => 'JToggleColumn',
          'filter' => CHtml::listData($model->yesNoList(), 'id', 'name'),
          'header' => 'Всегда беспл. доставка',
        ),
        //array('name' => 'visible', 'class' => 'JToggleColumn', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),
       ),
  ));