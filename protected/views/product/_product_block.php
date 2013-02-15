<?php
/**
 * @var Product $data
 */
?>
<li class="grid_4" data-id=<?php echo $data->id?>>
  <a href="<?php echo $data->url?>">
    <?php if( $data->getImages('small') ) {?>
      <img src="<?php echo $data->getImages('small')[0]?>" alt="<?php echo $data->name?>" title="<?php echo $data->name?>" style="width: 200px;" />
    <?php } ?>
    <span class="link-to-bike"><span><?php echo $data->name?></span></span>
  </a>
</li>
