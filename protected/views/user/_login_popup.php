<?php
/**
 * @var FController $this
 */
?>
<script>
  //<![CDATA[
  $(function(){
    $('.authorization-link').click(function(e){
      e.preventDefault();
      var target = $('#authorization-popup');
      $.overlayLoader(true, target);
    })
    $('#authorization-popup .new-user-btn').click(function(e){
      e.preventDefault();
      var target = $(this).closest('.popup');
      $.overlayLoader(false, target);
      target = $('#registration-popup');
      setTimeout(function(){
        $.overlayLoader(true, target);
      }, 300);
    })
  })
  //]]>
</script>
<div class="popup" id="authorization-popup">
  <a href="" class="close"></a>
  <div class="jurabold s24 uppercase m20">Вход для зарегистрированных пользователей</div>
  <?php echo $this->loginPopupForm?>
</div>