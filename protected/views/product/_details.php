<div class="details-top">
  <div class="s16 condensed uppercase m15">Характеристики:</div>
  <table class="zero short-details-table">
    <?php if( $category = $data->category ) { ?>
      <tr>
        <th>Бренд:</th>
        <td><?php echo $category->name?></td>
      </tr>
    <?php } ?>
    <?php foreach($data->getProductTabletParameters() as $parameter) { ?>
      <tr>
        <th><?php echo $parameter->name?>:</th>
        <td><?php echo $parameter->value?></td>
      </tr>
    <?php } ?>
  </table>
</div>
<div class="details-bottom">
  <div class="s16 condensed uppercase m7"><?php if( !empty($data->notice) ) { ?>Описание:<?php } ?></div>
  <div class="product-description s14 light">
    <?php echo $data->notice?>
  </div>
  <div class="nofloat">
    <?php echo $this->basket->buttonFastOrder($data, 'Купить в 1 клик', array('class' => 'btn turq-btn h32btn s16 fl one-click-btn'), array(
      'url' => $data->getUrl(),
      'price' => PriceHelper::price($data->getPrice(), ' <span class="currency">руб.</span>'),
      'img' => $data->getImage()->pre,
      'name' => $data->name
    ))?>
    <a href="<?php echo $data->getUrl()?>" class="btn black-btn h32btn s16 fr more-btn">Подробнее</a>
  </div>
</div>