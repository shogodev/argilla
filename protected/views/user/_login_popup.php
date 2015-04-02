<?php
/**
 * @var FController $this
 */
?>
<div class="popup login-popup" id="login-popup">
  <a href="" class="close"></a>
  <div class="popup-header">
    <div class="popup-header-inner">
      <div class="h1 s24 white uppercase m0">Вход в личный кабинет</div>
    </div>
  </div>
  <div class="popup-body">
    <?php echo $this->loginPopupForm;?>
  </div>
</div>
<script>
  $(function() {
    $('.auth-link').click(function(e) {
      e.preventDefault();
      var target = $('#login-popup');
      $.overlayLoader(true, target);
    });
  });
</script>