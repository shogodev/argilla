<?php
/**
 * @var Product $data
 * @var $this FController
 */
?>
<li>
  <div class="product">
    <div class="product-image m10">
      <?php if( $image = Arr::reset($data->getImages()) ) {?>
        <a href="<?php echo $data->url?>">
          <img alt="" src="<?php echo $image->pre?>">
        </a>
      <?php }?>
    </div>
    <a class="product-name center s16 m5 jurabold" href="<?php echo $data->url?>"><?php echo $data->name?></a>
    <?php if( $data->economy ) {?>
      <div class="economy center uppercase s15 m10 bb">Экономия <?php echo $data->economy?>%</div>
    <?php }?>
    <div class="nofloat center">
      <?php if( $data->economy ) {?>
        <div class="s15 m5 old-price"><?php echo Yii::app()->format->formatNumber($data->price_old)?> руб.</div>
      <?php }?>
      <div class="s20 jurabold price"><?php echo Yii::app()->format->formatNumber($data->sum)?> руб.</div>
    </div>
  </div>
</li>