<?php
/**
 * @var FForm $registrationForm
 * @var UserController $this
 */
?>
<div class="wrapper">
    <div class="breadcrumbs-offset m15">
      <?php $this->renderOverride('_breadcrumbs');?>
    </div>

  <?php if( Yii::app()->user->isGuest ) {?>
    <?php if( !$registrationForm->getStatus() ) {?>
        <div class="text-container"><?php echo $this->textBlock('registration_text');?></div>
     <?php } ?>

    <div class="auth-popup registration-block inline">
      <div class="auth-tabs" id="auth-tabs">
        <ul class="ui-tabs-nav">
          <li><a href="<?php echo $this->createUrl('user/login')?>">Вход</a></li>
          <li class="ui-tabs-active"><a>Регистрация</a></li>
        </ul>

        <div id="registration" class="tabs-inner">
           <div class="center s20 light m20">Регистрация через соц. сети</div>
           <?php $this->renderPartial('/user/_login_social', $_data_);?>
           <div class="center s25 light m20">или</div>
          <?php echo $registrationForm->render();?>
        </div>
      </div>
    </div>

  <?php } else {?>
    <?php echo "Вы уже зарегистрированы."?>
  <?php }?>
</div>