<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @var $this BOrderController
 * @var $model BOrder
 */
?>
<ul class="s-breadcrumbs breadcrumb">
  <li class="active">Оплата заказа</li>
</ul>

<?php $this->widget('BGridView', array(
  'dataProvider' => new CArrayDataProvider($model->payment ? array($model->payment) : array()),
  'template'     => "{filters}\n{items}\n{pagesize}\n{pager}\n{scripts}",
  'columns'      => array(
    array('name' => 'id', 'header' => '#', 'htmlOptions' => array('class' => 'span1 center')),
    array('name' => 'payment_id', 'header' => 'Оплата', 'value' => '$data ? $data->getTransactionUrl() : ""', 'type' => 'html'),
    array('name' => 'status', 'header' => 'Статус оплаты', 'value' => 'PlatronSystem::getStatus($data->status)'),
    array('name' => 'captured_status', 'header' => 'Статус клиринга'),
    array('class' => 'BOrderPaymentColumn'),
  ),
));