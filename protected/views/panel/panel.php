<?php
/**
 * @var FController $this
 */
?>
<?php
$this->basket->addAfterAjaxScript(new CJavaScriptExpression("
     if( action == 'add' )
     {
       image = element.closest('.product, .product-card, .full-view-container').find('.animate-image');
       $('#panel').panel('animate', image, 'headerRight');
     }
     $('#panel').panel('update', response);
  "));
?>
<?php
$this->favorite->addAfterAjaxScript(new CJavaScriptExpression("
     if( action == 'add' )
       $('#panel').panel('animate', element.closest('.product, .product-card, .full-view-container').find('.animate-image'), 'headerLeft');
     $('#panel').panel('update', response);
  "));
?>

<script>
  //<![CDATA[
  $(function(){
    $('#panel').panel({
      'controls' : {
        headerLeft : '#js-panel-header-left',
        headerRight : '#js-panel-header-right',
        bodyLeft : '#js-panel-body-left',
        bodyRight : '#js-panel-body-right',
        footerLeft: '#js-panel-footer-left',
        footerRight: '#js-panel-footer-right',
        carouselLeft : $('#js-panel-carousel-left').panelCarousel({
            controls : {
              container : '.js-carousel-container',
              buttonPrev: '.js-carousel-prev',
              buttonNext: '.js-carousel-next'
            },
            items: 5
          }
        ),
        carouselRight : $('#js-panel-carousel-right').panelCarousel({
            controls : {
              container : '.js-carousel-container',
              buttonPrev: '.js-carousel-prev',
              buttonNext: '.js-carousel-next'
            },
            items: 5
          }
        ),
        ajaxUpdateSelectors : ['.panel-share-basket']
      },
    });
  });
  //]]>
</script>
<div class="products-fixed-panel collapsed" id="panel">
  <div class="products-panel-header container">
    <a href="" class="panel-fav-link fl" id="js-panel-header-left">
      <i class="icon fav-icon"></i>
      <span class="s16 uppercase">Избранное <span class="fav-counter">(<?php echo $this->favorite->count()?>)</span></span>
      <i class="icon arrow-icon"></i>
    </a>
    <a href="" class="panel-basket-link fr" id="js-panel-header-right">
      <i class="icon basket-icon"></i>
      <span class="s16 uppercase">Товаров в корзине на сумму:</span>
      <span class="basket-counter">
        <span class="s24 bb"><?php echo PriceHelper::price($this->basket->getSumTotal())?></span> <span class="s14">руб.</span>
      </span>
      <i class="icon arrow-icon"></i>
    </a>
      <div class="panel-share-basket center fr" style="<?php echo $this->basket->isEmpty() ? 'display: none' : ''?>">
        <?php if( Yii::app()->user->isGuest ) {?>
          <a href="<?php echo Yii::app()->shareBasket->getHowToShareLink()?>" class="how-to-share dark-grey">Как поделиться корзиной?</a>
        <?php } else {?>
          <?php echo Yii::app()->shareBasket->ui->buttonShareBasket('<i class="icon share-icon"></i>поделиться корзиной', array('class' => 'share-basket dark-grey uppercase s16 dec-none')); ?>
        <?php }?>
      </div>
  </div>
  <div class="products-panel-body container">
    <a href="" class="close"></a>
    <?php $this->renderPartial('/panel/_tab_favorite')?>
    <?php $this->renderPartial('/panel/_tab_basket')?>
  </div>
</div>
