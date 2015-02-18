<?php
/**
 * @var Product $data
 * @var $this FController
 */
?>
<li class="product">
  <div class="product-name center nova m3">
    <a href="<?php echo $this->createUrl('product/section', array('section' => $data->section->url))?>" class="dark-grey s13"><?php echo $data->section->name;?></a>
    <a class="dark-grey uppercase bb s12" href="<?php echo $data->getUrl()?>"><?php echo $data->getHeader()?></a>
  </div>
  <div class="product-image">
    <?php echo $widget->skin['collection']->buttonRemove($data, '', array('class' => 'remove'))?>
    <?php if( $image = $data->getImage() ) {?>
      <a href="<?php echo $data->getUrl()?>"><img src="<?php echo $image->pre;?>" alt=""></a>
    <?php }?>
  </div>
  <div class="product-content center">
    <div class="m5">
      355 м²
    </div>
    <div class="m10">
      <span class="s30 bb"><?php echo PriceHelper::price($data->getSum())?></span> руб.
    </div>
  </div>
</li>