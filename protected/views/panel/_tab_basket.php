<?php
/**
 * @var FController $this
 */
?>
<div class="products-panel-content" id="basket-block">
  <div class="goods-carousel vitrine m15" id="basket-goods-carousel">
    <?php
    $this->widget('FListView', array(
      'id' => 'list-view-basket-panel',
      'dataProvider' => new FArrayDataProvider($this->basket, array('pagination' => false)),
      'itemsTagName' => 'ul',
      'itemsCssClass' => 'carousel',
      'htmlOptions' => array('class' => 'basket-list carousel-container'),
      'itemView' =>  '/panel/_item',
      'emptyText' => 'Корзина пуста',
    ));?>
    <?php $this->basket->addAfterAjaxScript(new CJavaScriptExpression("
      if( $('.".$this->basket->classWaitAction."').length != 0 )
        panelAnimation($('.".$this->basket->classWaitAction."').removeClass('".$this->basket->classWaitAction."'), $('.panel-basket-link'));
    "));?>
    <a href="#" class="carousel-controls carousel-prev disabled"></a>
    <a href="#" class="carousel-controls carousel-next"></a>
  </div>
  <script>
    //<![CDATA[
    $(function() {
      var container = '#basket-goods-carousel';
      if ( $('.carousel li', container).length > 6 ) {
        $('.carousel-container', container).jcarousel({
          'animation' : 'fast'
        });
        $('.carousel-next, .carousel-prev', container).show();
        $('.carousel-controls').on('jcarouselcontrol:active', function(event, carousel) {
          $(this).removeClass('disabled');
        }).on('jcarouselcontrol:inactive', function(event, carousel) {
            $(this).addClass('disabled');
          })
        $('.carousel-prev', container).jcarouselControl({
          target: '-=1'
        });
        $('.carousel-next', container).jcarouselControl({
          target: '+=1'
        });
      }
    });
    //]]>
  </script>
  <div class="nofloat" style="padding: 0 10px">
    <div class="fl" style="padding-top: 5px">
      В Вашем заказе <span class="bb"><?php echo $this->basket->countAmount()?></span> товаров а сумму <span class="bb"><?php echo Yii::app()->format->formatNumber($this->basket->totalSum())?> руб.</span>
    </div>
    <a href="<?php echo $this->createUrl('basket/index')?>" class="fr btn red-btn wide-paddings-btn">Оформить заказ</a>
  </div>
</div>