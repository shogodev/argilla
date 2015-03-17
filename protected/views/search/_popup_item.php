<?php
/**
 * @var string $name
 * @var string $image
 * @var string $price
 * @var string $articul
 */
?>
<div class="nofloat">
  <?php if( $image ) {?>
    <div class="autocomplete-pic fl">
      <img src="<?php echo $image;?>"/>
    </div>
  <?php }?>
  <div class="autocomplete-content fl">
    <span><?php echo $name;?></span>
    <br/><?php echo $articul;?>
    <?php if( PriceHelper::isNotEmpty($price)  ) {?>
      <div class="autocomplete-price bb"><?php echo PriceHelper::price($price, ' руб.');?></div>
    <?php }?>
  </div>
</div>