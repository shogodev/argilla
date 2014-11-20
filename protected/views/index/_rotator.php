<?php
/**
 * @var IndexController $this
 * @var Banner[]|array $rotator
 */
?>
<?php if( !empty($rotator) ) { ?>
  <div class="rotator-wrapper main-rotator-wrapper">
    <div id="main-rotator" class="cycle-slideshow"
         data-cycle-slides="> a"
         data-cycle-speed="500"
         data-cycle-timeout="5000"
         data-cycle-update-view="1">
      <?php foreach($rotator as $banner) { ?>
        <a href="<?php echo $banner->url?>"><img src="<?php echo $banner->image?>" alt="<?php echo CHtml::encode($banner->title)?>" /></a>
      <?php }?>
      <div class="cycle-pager"></div>
    </div>
  </div>
<?php }?>

