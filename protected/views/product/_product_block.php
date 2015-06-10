<?php
/**
 * @var FController $this
 * @var Product $data
 */
?>
<div class="product">
  <a href="<?php echo $data->getUrl()?>" class="product-image">
    <?php if( $image = $data->getImage() ) { ?>
      <img src="<?php echo $image->pre?>" alt="<?php echo $data->getHeader()?>" />
    <?php } ?>
  </a>

  <div class="product-icons">
    <?php if( $data->spec ) { ?>
      <span class="leader">ХИТ</span>
    <?php } ?>
    <?php if( $data->novelty ) { ?>
      <span class="new">NEW</span>
    <?php } ?>
  </div>

  <div class="product-name-section">
    <?php if( $type = $data->type ) { ?>
      <div class="product-type"><?php echo $type->name?></div>
    <?php } ?>
    <div class="product-name">
      <a href="<?php echo $data->getUrl()?>"><?php echo $data->getHeader()?></a>
    </div>
  </div>

  <?php if( $data->dump ) {?>
    <?php echo $data->getDumpName();?>
  <?php } else {?>
    <?php echo $data->getDumpName();?>
  <?php }?>

  <div class="product-price-block">
    <?php if( $economy = PriceHelper::getEconomyPercent($data->getPriceOld(), $data->getPrice()) ) { ?>
      <div class="economy">- <?php echo $economy?>%</div>
    <?php } ?>
    <?php if( PriceHelper::isNotEmpty($data->getPriceOld()) ) { ?>
      <div class="old-price"><?php echo PriceHelper::price($data->getPriceOld(), ' <span class="currency">руб.</span>')?></div>
    <?php } ?>
    <div class="price">
      <span class="price-label">Цена:</span> <?php echo PriceHelper::price($data->getPrice(), ' <span class="currency">руб.</span>')?>
    </div>
  </div>

   <?php echo $this->basket->buttonAdd($data, array('Купить', 'В корзине'), array('class' => 'btn blue-btn cornered-btn h32btn s19 uppercase buy-btn', 'href' => $this->createUrl('order/firstStep')))?>

  <span class="details-btn js-show-product-details" data-url="<?php echo $data->getUrl();?>">Детали</span>
  <div class="product-details">
    <span class="js-hide-product-details close">[X]</span>
    <div class="js-product-details-data"></div>
  </div>
</div>