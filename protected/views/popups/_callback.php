<?php
/**
 * @var FController $this
 */
?>
<script>
  //<![CDATA[
  $(function(){
    $('.callback-link').click(function(e){
      e.preventDefault();
      var target = $('#callback-popup');
      $.overlayLoader(true, target);
    })
  });
  //]]>
</script>
<div class="popup" id="callback-popup">
  <a href="" class="close"></a>
  <div class="jurabold s24 uppercase m20">Заказать звонок</div>
  <?php echo $this->callbackForm?>
</div>