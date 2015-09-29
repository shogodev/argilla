<?php
/**
 * @var FController $this
 */
?>

<header class="header">

  <!-- logo -->
  <div class="header--logo">
    <a href="<?php echo $this->createUrl('index/index')?>" rel="home">
      Argilla
    </a>
  </div>

  <!-- top menu -->
  <nav class="menu">
    <?php ViewHelper::menu($this->getMenuBuilder()->getMenu('top'))?>
  </nav>

  <?php if( ViewHelper::contact() ) {?>

   <div class="header--phones">
    <!-- phones -->
    <?php foreach(ViewHelper::phones() as $phone) {?>
       <a href="tel:<?php echo $phone->getClearPhone();?>">
         <?php echo $phone->value.$phone->description;?>
       </a>
    <?php }?>
   </div>

    <!-- worktime -->
    <?php if( $workTime = ViewHelper::contact()->notice ) {?>
      <?php echo $workTime;?>
    <?php }?>
  <?php }?>

  <!-- basket -->
  <?php $this->renderPartial('/_basket_header');?>

  <!-- search -->
  <div class="header--search">
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

  <!-- login/signup -->
  <div class="header--auth">
    <?php if( Yii::app()->user->isGuest ) { ?>
      <a href="<?php echo $this->createUrl('user/login'); ?>">
        Вход
      </a>
      <a href="<?php echo $this->createUrl('user/registration') ?>">
        Регистрация
      </a>
    <?php } else { ?>
      <a href="<?php echo $this->createUrl('user/data'); ?>">
        <?php echo Yii::app()->user->name?>
      </a>
      <a href="<?php echo $this->createUrl('user/logout') ?>">
        Выйти
      </a>
    <?php }?>
  </div>
</header>