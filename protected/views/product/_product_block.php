<?php
/**
 * @var FController $this
 * @var Product $data
 */
?>

<div class="product">
  <a href="<?php echo $data->getUrl();?>" class="product-image">
    <img src="f/product/product-1.jpg" alt="" />
  </a>
  <div class="product-icons">
    <?php if( PriceHelper::isNotEmpty($data->getPriceOld()) ) {?>
      <span class="discount"></span>
    <?php }?>
    <?php if( PriceHelper::isNotEmpty($data->novelty) ) {?>
      <span class="new"></span>
    <?php }?>
    <?php if( PriceHelper::isNotEmpty($data->spec) ) {?>
      <span class="leader"></span>
    <?php }?>
  </div>
  <div class="product-article-line">
    <?php if( !empty($data->articul) ) { ?>
      <div class="product-article">Артикул: <?php echo $data->articul?></div>
    <?php } ?>
    <div class="<?php echo $data->getDumpClass();?>"><?php echo $data->getDumpName();?></div>
  </div>
  <div class="product-name">
    <?php echo CHtml::link($data->name, $data->getUrl())?>
  </div>
  <?php $this->renderPartial('/product/_price_block', array('data' => $data));?>
  <div class="product-buy-block">
    <?php echo $this->basket->buttonAdd($data, 'Купить', array('class' => 'btn rounded-btn red-contour-btn h29btn buy-btn opensans s14 bb uppercase add-through-popup-basket'))?>
  </div>

  <?php if( $widget->skin != 'compare' ) {?>
    <?php echo $this->compare->buttonAdd($data, array('в сравнение', 'в сравнении'), array('class' => 'btn compare-link'))?>
  <?php } else {?>
    <?php echo $this->compare->buttonRemove($data, '', array('class' => 'remove'))?>
  <?php }?>
</div>