<?php
/**
 * @var FController $this
 */
?>
<?php $this->basket->ajaxUpdate(array('panel-block'));?>
<?php $this->basket->addBeforeAjaxScript(new CJavaScriptExpression("
  var panel = $('.products-fixed-panel');
  if( panel.length && !panel.hasClass('collapsed') )
    panel.addClass('collapsed');
"));?>

<div class="products-fixed-panel collapsed" id="panel-block">
  <?php if( !$this->basket->isEmpty() || !$this->visits->isEmpty() ) { ?>
    <div class="products-panel-header nofloat" id="panel-amount-block">
      <?php if( !$this->visits->isEmpty() ) { ?>
        <a href="" class="panel-fav-link fl">Вы смотрели<span class="fav-counter"><?php echo $this->visits->count()?></span></a>
      <?php }?>
      <?php if( !$this->basket->isEmpty() ) { ?>
        <a href="" class="panel-basket-link fr">Корзина<span class="basket-counter"><?php echo $this->basket->countAmount()?></span></a>
      <?php }?>
    </div>
    <div class="products-panel-body">
    <a href="" class="close"></a>
      <?php $this->renderPartial('/panel/_tab_favorite')?>
      <?php $this->renderPartial('/panel/_tab_basket')?>
    </div>
  <?php }?>
  <script>
    //<![CDATA[
    var panelAnimation = function(element, targetBlock)
    {
      // Анимация полета товара к панели
      var pic = element.closest('.product').find('.animate-product-image:first');

      if ( pic.size() == 0 )
        pic = $('.animate-product-image-card:first');

      var clonedPic = pic.clone().css({
        position: 'fixed',
        left: pic.offset().left - $(window).scrollLeft(),
        top: pic.offset().top - $(window).scrollTop(),
        height: pic.height(),
        width: pic.width()
      }).addClass('movin-to-basket');
      $('body').append(clonedPic);

      var targetPos = { top: targetBlock.offset().top - $(window).scrollTop(), left: targetBlock.offset().left - $(window).scrollLeft() };
      clonedPic.css({
        top: targetPos.top,
        left: targetPos.left,
        opacity: 0
      }).addClass('scale');
      setTimeout(function(){
        clonedPic.remove();
      }, 500)
    };

    $('.panel-fav-link, .panel-basket-link').click(function(e){
      e.preventDefault();
      if ( $(this).hasClass('active') ) {
        $('.products-fixed-panel').addClass('collapsed');
      } else {
        $('.products-fixed-panel').removeClass('collapsed');
        if ( $(this).hasClass('panel-fav-link') ) {
          $('#basket-block').stop(true,true).hide();
          $('#favorites-block').stop(true,true).fadeIn();
        } else {
          $('#favorites-block').stop(true,true).hide();
          $('#basket-block').stop(true,true).fadeIn();
        }
      }
      $(this).siblings().removeClass('active');
      $(this).toggleClass('active');
    });

    $('.products-fixed-panel .close').click(function(e){
      e.preventDefault();
      $('.products-fixed-panel').addClass('collapsed');
      $('.panel-fav-link, .panel-basket-link').removeClass('active');
    })
    //]]>
  </script>
</div>
