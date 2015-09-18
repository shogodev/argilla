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
<h1>Товары</h1>

<table width=100% border=0 cellspacing=1 cellpadding=4 class="bord">
  <tr>
    <td width="80%">
      <b>Наименование</b>
    </td>
    <td nowrap>
      <b>Размер</b>
    </td>
    <td nowrap>
      <b>Цена, руб.</b>
    </td>
    <td nowrap>
      <b>Кол-во, шт.</b>
    </td>
    <td nowrap>
      <b>Скидка</b>
    </td>
    <td nowrap>
      <b>Стоимость, руб.</b>
    </td>
  </tr>

  <?php $totalCount = 0;?>
  <?php $totalDiscount = 0;?>
  <?php foreach($model->products as $product) {?>
    <tr>
      <td><?php echo $product->name?></td>
      <td nowrap><?php echo $product->getItem('ProductParameter');?></td>
      <td nowrap><?php echo PriceHelper::price($product->price, '', '')?></td>
      <td nowrap><?php echo PriceHelper::price($product->count)?><?php $totalCount+=$product->count;?></td>
      <td nowrap><?php echo PriceHelper::price($product->discount, '', '') ?><?php $totalDiscount+=$product->discount;?></td>
      <td nowrap><?php echo PriceHelper::price($product->sum, '', '')?></td>
    </tr>
  <?php }?>

  <tr>
    <td align=right colspan="3">
      <b>Итого:</b>
    </td>
    <td nowrap>
      <b><?php echo PriceHelper::price($totalCount, '', '')?></b>
    </td>
    <td nowrap>
      <b><?php echo PriceHelper::price($totalDiscount, '', '')?></b>
    </td>
    <td nowrap>
      <b><?php echo PriceHelper::price($model->totalSum)?></b>
    </td>
  </tr>
  <?php if( PriceHelper::isNotEmpty($model->delivery->delivery_price) ) {?>
    <tr>
      <td align=right colspan="5">
        <b>Стоимость доставка:</b>
      </td>
      <td nowrap>
        <b><?php echo PriceHelper::price($model->delivery->delivery_price)?></b>
      </td>
    </tr>
  <?php }?>
</table>