<?php
/**
 * @var NewsController $this
 * @var News $data
 */
?>
<article class="news-announce m50">
  <?php if( $data->image ) { ?>
    <a href="<?php echo $data->getUrl()?>" class="news-pic">
      <img src="<?php echo $data->image->pre?>" alt="" />
    </a>
  <?php } ?>
  <div class="no-overflow">
    <div class="nofloat m20">
      <a href="<?php echo $data->getUrl()?>" class="h2 fl"><?php echo $data->name?></a>
      <?php if( $data->section_id == NewsSection::ID_NEWS ) {?>
        <time datetime="<?php echo $data->getFormatDate('Y-m-d')?>" class="fr"><?php echo $data->getFormatDate()?></time>
      <?php }?>
    </div>
    <div class="text-container">
      <?php echo $data->notice?>
    </div>
  </div>
</article>