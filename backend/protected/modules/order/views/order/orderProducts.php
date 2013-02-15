<?php
/* @var $this BOrderController */
/* @var $model BOrder */
?>

<?php $this->widget('BGridView', array(
  'dataProvider' => new CArrayDataProvider($model->products),
  'template'     => "{filters}\n{items}\n{pagesize}\n{pager}\n{scripts}",
  'columns'      => array(
    array('name' => 'name', 'header' => 'Название'),
    array('name' => 'price', 'header' => 'Цена', 'type' => 'number'),
    array('name' => 'count', 'header' => 'Количество'),
    array('name' => 'discount', 'header' => 'Скидка', 'htmlOptions' => array('class' => 'center span1'), 'type' => 'number'),
    array('name' => 'sum', 'header' => 'Сумма', 'type' => 'number'),
  ),
)); ?>