<?php
/**
 * @var BasketController $this
 * @var array $_data_
 */
?>
<table>
  <tr>
    <th>Наименование</th>
    <th>Цена, руб.</th>
    <th>Количество</th>
    <th>Стоимость, руб.</th>
    <th>Удалить</th>
  </tr>

  {items}

</table>

<table>
  <tr>
    <td>
      Стоимость выбранных товаров:
    </td>
    <td>
      <?php echo PriceHelper::price($this->basket->getSumTotal(), ' руб.')?>
    </td>
  </tr>
</table>