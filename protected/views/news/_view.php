<?php
/**
 * @var NewsController $this
 * @var News $data
 */
?>
<article class="news_short clearfix m20">

  <?php if( $data->image ) { ?>
  <img src="<?php echo $data->image?>" alt="" class="img-polaroid" style="float: left; width: 110px; margin: 0 10px 10px 0" />
  <?php } ?>

  <div class="m40">
    <div class="lead"><?php echo $data->date?></div>
    <div class="lead"><a href="<?php echo $data->url?>"><?php echo $data->name?></a></div>
    <div class="m10"><?php echo $data->notice?></div>

    <?php $this->widget('bootstrap.widgets.TbButton', array(
      'size' => 'mini',
      'url' => $data->url,
      'label' => 'Подробнее',
    )); ?>
  </div>
</article>