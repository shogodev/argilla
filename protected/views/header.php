<?php
/**
 * @var FController $this
 */
?>
<header class="wrapper nofloat" style="background-color: #F4F4F4;">

  <div class="nofloat m10">

    <a class="hd-logo" href="<?php echo $this->createUrl('index/index')?>">
      <img width="92" height="109" style="border: solid 1px #000000;" src="i/sp.gif" />
    </a>

    <div class="fr">
      <?php if( Yii::app()->user->isGuest ) { ?>
        <a href="<?php echo $this->createUrl('user/login'); ?>"><span>Вход</span></a>
        <a href="<?php echo $this->createUrl('user/registration') ?>"><span>Регистрация</span></a>
      <?php } else { ?>
        <a href="<?php echo $this->createUrl('user/data'); ?>"><span><?php echo Yii::app()->user->name?></span></a>
        <a href="<?php echo $this->createUrl('user/logout') ?>"><span>Выйти</span></a>
      <?php } ?>
    </div>

  </div>

  <div class="nofloat m10">
    <?php $this->widget('FMenu', array('items' => $this->getTopMenu()))?>
  </div>

</header>
