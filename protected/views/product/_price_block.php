<?php
/**
 * @var Product $data
 */
?>
<?php if( $economy = PriceHelper::getEconomyPercent($data->getPriceOld(), $data->getPrice()) ) {?>
  <div class="product-discount">
    <span>-<?php echo $economy;?></span>%
  </div>
<?php }?>
<div class="product-price-block">
  <?php if( PriceHelper::isNotEmpty($data->getPriceOld()) ) {?>
    <div class="old-price"><?php echo PriceHelper::price($data->getPriceOld(), ' руб.');?></div>
  <?php }?>
  <div class="product-price"><?php echo PriceHelper::price($data->getPrice(), ' руб.');?></div>
</div>