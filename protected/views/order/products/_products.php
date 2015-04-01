<?php
/**
 * @var BasketController $this
 * @var array $_data_
 */
?>
<table class="zero basket-table m10">
  <tr>
    <th>Наименование</th>
    <th>Цена, руб.</th>
    <th>Количество</th>
    <th>Стоимость, руб.</th>
    <th>Удалить</th>
  </tr>

  {items}

</table>

<table class="zero basket-total-table m10">
  <tr>
    <td>
      <span class="s24 bb uppercase">Стоимость выбранных товаров:</span>
    </td>
    <td class="s13 grey">
      <span class="s26 bb red"><?php echo PriceHelper::price($this->basket->getSumTotal(), ' руб.')?></span>
    </td>
  </tr>
</table>