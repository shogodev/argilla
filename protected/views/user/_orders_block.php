<?php
/**
 * @var Order $data
 */
?>
<div class="order">
  <div class="nofloat m10">
    <p class="s20 bb fl">Заказ <?php echo $data->id?> от <?php echo date("d.m.Y", strtotime($data->date_create))?>, сумма <?php echo Yii::app()->format->formatNumber($data->sum)?> руб.</p>
  </div>
  <p>Статус:</p>
  <table class="order-status m20">
    <tr>
      <?php foreach( OrderStatus::model()->findAll() as $status ) { ?>
        <td>
          <span class="step-<?php echo $status->id?> <?php echo( $status->id === $data->status_id ) ? 'active' : ''?>">
            <?php echo $status->name?>
          </span>
        </td>
      <?php } ?>
    </tr>
  </table>
  <table class="order-details m20">
    <thead>
    <th width="">Наименование</th>
    <th width="">Артикул</th>
    <th width="">Цена</th>
    <th width="">Количество</th>
    <th width="">Сумма</th>
    </thead>
    <tbody>
    <?php foreach($data->products as $product) {?>
      <tr class="product-name-row">
        <td class="bb"><?php echo $product->name?></td>
        <td><?php if( isset($product->history) ) echo $product->history->articul?></td>
        <td>
          <?php echo Yii::app()->format->formatNumber($product->price)?>
          <?php if( $areaItem = $product->getItem('customParameterArea') ) {?> руб. за уп.<?php }?>
        </td>
        <td>
          <?php if( $areaItem ) {?>
            <?php echo $product->count?> уп. = <?php echo $product->getItem('customParameterFullArea')->value ?> м&sup2;
          <?php } else {?>
           <?php echo $product->count?>
          <?php }?>
        </td>
        <td class="bb"><?php echo Yii::app()->format->formatNumber($product->sum).' руб.'?></td>
      </tr>
      <?php foreach($product->getItems() as $item) {?>
        <tr class="s12">
          <td colspan="5"><?php echo $item->name?>: <?php echo $item->value?></td>
        </tr>
      <?php }?>
    <?php }?>
    </tbody>
    <tfoot>
    <tr class="order-result bb">
      <td colspan="4">Итого:</td>
      <td><?php echo Yii::app()->format->formatNumber($data->sum)?> руб.</td>
    </tr>
    </tfoot>
  </table>
</div>