<?php
/**
 * @var FController $this
 */
?>
<div class="popup auth-popup" id="auth-popup">
  <a href="" class="close"></a>
  <div class="auth-tabs" id="auth-tabs">
    <ul class="ui-tabs-nav">
      <li class="ui-tabs-active"><a>Вход</a></li>
      <li><a href="<?php echo $this->createUrl('user/registration')?>">Регистрация</a></li>
    </ul>

    <div id="login" class="tabs-inner">
      <div class="center s20 light m20">Войти через соц. сети</div>
      <?php $this->renderPartial('/user/_login_social', $_data_);?>
      <div class="center s25 light m20">или</div>
      <?php echo $this->loginPopupForm;?>
    </div>

  </div>
</div>