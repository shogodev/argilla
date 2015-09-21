<?php
/**
 * @var UserController $this
 * @var FForm $loginForm
 */
?>
<div class="wrapper">
    <div class="breadcrumbs-offset m15">
      <?php $this->renderOverride('_breadcrumbs');?>
    </div>

  <div class="auth-popup inline">
    <div class="auth-tabs" id="auth-tabs">
      <ul class="ui-tabs-nav">
        <li class="ui-tabs-active"><a>Вход</a></li>
        <li><a href="<?php echo $this->createUrl('user/registration')?>">Регистрация</a></li>
      </ul>

      <div id="login" class="tabs-inner">
        <div class="center s20 light m20">Войти через соц. сети</div>
        <?php $this->renderPartial('/user/_login_social', $_data_);?>
        <div class="center s25 light m20">или</div>
        <?php echo $loginForm->render();?>
      </div>

    </div>
  </div>
</div>
