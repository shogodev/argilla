<?php
/**
 * @var FController $this
 */
?>
<script>
  //<![CDATA[
  $(function(){
    $('body').on('click', '.one-click-btn', function(e){
      e.preventDefault();
      var target = $('#one-click-popup');
      $.overlayLoader(true, target);

      $('.one-click-phone').mask('+7 (999) 999-99-99');
    })
  })
  //]]>
</script>
<div class="popup popup-with-form" id="one-click-popup">
  <a href="" class="close"></a>
  <div class="jurabold s24 uppercase m20">Купить в 1 клик</div>
  <div id="<?php echo $this->basket->fastOrderFormSuccessId?>" style="display: none"><?php echo $this->textBlockRegister('Успешный быстрый заказ')?></div>
  <?php echo $this->fastOrderForm?>
</div>