<?php
/* @var $this BOrderController */
/* @var $model BOrder */
?>

<ul class="s-breadcrumbs breadcrumb">
  <li class="active">История изменения статуса</li>
</ul>

<?php $this->widget('BGridView', array(
  'dataProvider' => new CArrayDataProvider($model->history),
  'template'     => "{filters}\n{items}\n{pagesize}\n{pager}\n{scripts}",
  'columns'      => array(
    array('name' => 'id', 'value' => '$row + 1', 'header' => '#', 'htmlOptions' => array('class' => 'span1 center')),
    array('name' => 'user_id', 'value' => '$data->user ? $data->user->username : ""', 'header' => 'Пользователь'),
    array('name' => 'date', 'header' => 'Дата', 'type' => 'datetime'),
    array('name' => 'old_status', 'value' => '$data->old_status ? $data->old_status->name : ""', 'header' => 'Старый статус'),
    array('name' => 'new_status', 'value' => '$data->new_status ? $data->new_status->name : ""', 'header' => 'Новый статус'),
  ),
));