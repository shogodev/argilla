<?php
/**
 * @var IndexController $this
 * @var Banner[]|array $rotator
 */
?>
<?php if( !empty($rotator) ) { ?>
  <div class="rotator-wrapper main-rotator-wrapper">
    <div id="main-rotator" class="main-rotator js-slideshow">
      <?php foreach($rotator as $banner) { ?>
        <a href="<?php echo $banner->url?>"><img src="<?php echo $banner->image?>" alt="<?php echo CHtml::encode($banner->title)?>" /></a>
      <?php }?>
    </div>
  </div>
<?php }?>