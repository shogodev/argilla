<?php
/* @var $this BOrderController */
/* @var $model BOrder */
?>

<ul class="s-breadcrumbs breadcrumb">
  <li class="active">Товары</li>
</ul>

<?php $this->widget('BGridView', array(
  'dataProvider' => new CArrayDataProvider($model->getProducts(), array('pagination' => false)),
  'template' => "{filters}\n{items}\n{pagesize}\n{pager}\n{scripts}",
  'rowCssClassExpression' => '$data instanceof BOrderProduct ? "group" : ($row % 2 ? "odd" : "even" )',
  'columns' => array(
    array('name' => 'fullName', 'header' => 'Название', 'type' => 'html'),
    array('name' => 'price', 'header' => 'Цена', 'value' => 'PriceHelper::isNotEmpty($data->price) ? PriceHelper::price($data->price) : ""'),
    array('name' => 'count', 'header' => 'Количество', 'value' => '!empty($data->count) ? $data->count : ""'),
    array('name' => 'discount', 'header' => 'Скидка', 'htmlOptions' => array('class' => 'center span1'), 'value' => 'PriceHelper::isNotEmpty($data->discount) ? floatval($data->discount) : ""'),
    array('name' => 'sum', 'header' => 'Сумма', 'value' => 'PriceHelper::isNotEmpty($data->sum) ? PriceHelper::price($data->sum) : ""'),
  ),
)); ?>