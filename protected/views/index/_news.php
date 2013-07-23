<?php
/**
 * @var News $data
 * @var integer $index
 */ ?>
<div class="column">
  <a href="<?php echo $data->section->url?>" class="caption m15"><?php echo $data->section->name?></a>
  <div class="announce-image m5">
    <?php if($data->image) {?>
      <a href="<?php echo $data->url?>"><img src="<?php echo $data->image->pre?>" alt=""></a>
    <?php }?>
  </div>
  <a href="<?php echo $data->url?>" class="bb s14 announce-name">
    <?php if( $data->section_id == NewsSection::ID_NEWS ) {?>
      <time datetime="<?php echo date('Y-m-d', strtotime($data->dateRaw))?>"><?php echo  $data->date?></time>
    <?php } else {?>
      <?php echo $data->name?>
    <?php }?>
  </a>
  <div class="text-container">
    <?php echo $data->notice?>
    <a href="<?php echo $data->url?>" class="more-link"></a>
  </div>
</div>