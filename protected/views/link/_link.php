<?php
/**
 * @var LinkController $this
 * @var Link $data
 */
?>
<li>
  <div class="bb m3">
    <a href="<?php echo $data->url?>" target="_blank"><?php echo $data->title?></a>
  </div>
  <div class="m3">
    <?php echo $data->content?>
  </div>
  <div class="s11 m20">
    <a href="<?php echo $data->url?>" target="_blank"><?php echo $data->url?></a>
  </div>
</li>