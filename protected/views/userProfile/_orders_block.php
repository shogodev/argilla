<?php
/**
 * @var Order $data
 */
?>
<div class="m30">
  <div class="nofloat order-header">
    <div class="fl">Заказ № <span class="red"><?php echo $data->id?></span> от <?php echo date("d.m.Y", strtotime($data->date_create))?>, сумма <?php echo PriceHelper::price($data->sum, ' руб.');?>
      <span class="red"></span>
    </div>
    <div class="fr">
      Статус заказа: <span class="red"><?php echo $data->status?></span>
    </div>
  </div>
  <table class="zero history-table">
    <tr>
      <th>Название</th>
      <th>Артикул</th>
      <th>Количество</th>
      <th>Стоимость, руб.</th>
    </tr>
    <?php foreach($data->products as $product) {?>
      <tr>
        <td><?php echo $product->name?></td>
        <td><?php if( isset($product->history) ) echo $product->history->articul?></td>
        <td><?php echo $product->count?></td>
        <td><?php echo PriceHelper::price($product->sum, ' руб.')?></td>
      </tr>
    <?php }?>
  </table>
  <div class="nofloat">
    <div class="fr s14">
      <?php if( PriceHelper::isNotEmpty($data->deliveryPrice) ) {?>
        <div class="m3">Стоимость доставки: <span class="red"><?php echo PriceHelper::price($data->deliveryPrice, ' руб.');?></span></div>
      <?php }?>
      Итог: <span class="red"><?php echo PriceHelper::price($data->totalSum, ' руб.');?></span>
    </div>
  </div>
</div>