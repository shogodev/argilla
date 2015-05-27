<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @var BOrder $model
 */
?>
<div style="text-align:center;padding-bottom:20px">
  <h1><?php echo Yii::app()->params->project?></h1>
</div>


<table width=100% border=0 cellspacing=1 cellpadding=4 align=center class="bord">
  <colgroup>
    <col width="200">
    <col>
  </colgroup>

  <?php BPrintHelper::rowHeader('Контактные данные');?>
  <?php BPrintHelper::modelRow($model, 'name');?>
  <?php BPrintHelper::modelRow($model, 'email');?>
  <?php BPrintHelper::modelRow($model, 'phone');?>
  <?php BPrintHelper::modelRow($model, 'address');?>

  <?php BPrintHelper::rowHeader('Заказ');?>
  <?php BPrintHelper::modelRow($model, 'id', '№');?>
  <?php BPrintHelper::modelRowList($model, 'status_id', null, BOrderStatus::model()->listData());?>
  <?php BPrintHelper::modelRowList($model, 'delivery_id', null, BOrderDeliveryType::model()->listData());?>
  <?php BPrintHelper::modelRow($model, 'comment');?>
  <?php BPrintHelper::modelRow($model, 'order_comment');?>

</table>


<?php $this->renderPartial('_print_products', $_data_);?>

<h1>Оплата заказа</h1>

<table width=100% border=0 cellspacing=1 cellpadding=4 class="bord">
  <?php BPrintHelper::modelRowList($model, 'payment_id', null, BOrderPaymentType::model()->listData());?>
</table>