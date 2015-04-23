<?php
/**
 * @var FController $this
 */
?>
<header id="header" class="no-print">
  <div class="container nofloat">
    <div class="header--logo fl">
      <a href="<?php echo $this->createUrl('index/index')?>" rel="home">Argilla</a>
    </div>
    <div class="header--top-row m7 fl">
      <nav id="top-menu" class="menu top-menu inline-menu s12">
        <?php ViewHelper::menu($this->getMenuBuilder()->getMenu('top'))?>
      </nav>
      <div class="header--note fl">
        <span class="s18 blue upcase">Интернет-магазин</span>
      </div>
      <?php if( ViewHelper::contact() ) {?>
        <div class="header--phone center fl">
          <?php foreach(ViewHelper::phones() as $phone) {?>
            <a class="nova s24 black m3" href="tel:<?php echo $phone->getClearPhone();?>"><?php echo $phone->value.$phone->description;?></a>
          <?php }?>
          <?php if( $workTime = ViewHelper::contact()->notice ) {?>
            <span class="s12"><?php echo $workTime;?></span>
          <?php }?>
        </div>
      <?php }?>
      <div class="header--links center fl">
        <a class="green js-overlay" href="#callback-popup">
          Заказать звонок
        </a>
      </div>
      <?php $this->renderPartial('/_basket_header');?>
    </div>
    <div class="header--bottom-row">
      <form action="/search/" method="get" class="header--search fl">
        <?php
          $this->widget('SearchWidget', array(
            'htmlOptions' => array(
              'class' => 'inp',
              'placeholder' => 'Поиск...'
            )
          ));
        ?>
        <input type="submit" value="Найти">
      </form>
    </div>
  </div>

  <div class="fr">
    <?php if( Yii::app()->user->isGuest ) { ?>
      <a href="<?php echo $this->createUrl('user/login'); ?>" class="auth-link"><span>Вход</span></a>
      <a href="<?php echo $this->createUrl('user/registration') ?>"><span>Регистрация</span></a>
    <?php } else { ?>
      <a href="<?php echo $this->createUrl('user/data'); ?>"><span><?php echo Yii::app()->user->name?></span></a>
      <a href="<?php echo $this->createUrl('user/logout') ?>"><span>Выйти</span></a>
    <?php } ?>
  </div>
</header>
