<?php
/**
 * @var $this FController
 */
?>
<div class="popup" id="callback-btn-content" style="display: none">
  <div class="popup-inner">
    <a class="close-btn" href=""></a>
    <div class="m20">
      <h3>Заказ обратного звонка</h3>
    </div>

    <?php echo $this->callbackForm->render();?>

  </div>
</div>

<script type="text/javascript">
  //<![CDATA[
  $(function() {
    // Форма заказа обратного звонка
    $('.callback-btn').data('popupWindow', true).on('click', function(e) {
      e.preventDefault();
      togglePopup($(this));
    });
  });
  //]]>
</script>
