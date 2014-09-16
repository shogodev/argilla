<?php
/**
 * @var IndexController $this
 * @var array $_data_
 */
?>
<div class="wrapper">
  <?php $this->renderPartial('_rotator', $_data_)?>

  <div class="text-container big-paddings dark-grey">
    <?php echo $this->textBlockRegister('Текст на главной после ротатора')?>
  </div>

  <?php if($this->beginCache('index/showcase', array('duration' => 300))) { ?>
    <?php $this->renderPartial('_showcase', array('showcase' => new Showcase(10))); ?>
  <?php $this->endCache(); } ?>

  <div class="hr1"></div>
  <div class="text-container big-paddings dark-grey">
    <?php echo $this->textBlockRegister('Текст на главной')?>
  </div>
  <div class="hr1 m20"></div>

</div>