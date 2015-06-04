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
        array('name' => 'price', 'header' => 'Стоимость', 'class' => 'OnFlyEditField', 'filter' => false),
        //array('name' => 'visible', 'class' => 'JToggleColumn', 'filter' => CHtml::listData($model->yesNoList(), 'id', 'name')),
       ),
  ));