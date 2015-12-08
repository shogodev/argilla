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
       $('#js-argilla-panel')['argillaPanel']('animate', image, 'right');
     }
     $('#js-argilla-panel')['argillaPanel']('update', response);
  "));

$this->favorite->addAfterAjaxScript(new CJavaScriptExpression("
     if( action == 'add' )
       $('#js-argilla-panel')['argillaPanel']('animate', element.closest('.product, .product-card, .full-view-container').find('.animate-image'), 'left');
     $('#js-argilla-panel')['argillaPanel']('update', response);
  "));
?>

<script language="JavaScript">
  $(function() {
    $('#js-argilla-panel')['argillaPanel']({
      selectors : {
        'left': {
          header: '#js-panel-header-left',
          body: '#js-panel-body-left',
          footer: '#js-panel-footer-left',
          carousel: $('#js-panel-carousel-left').panelCarousel({
            controls : {
              container : '.js-carousel-container',
              buttonPrev: '.js-carousel-prev',
              buttonNext: '.js-carousel-next'
            },
            items: 5
          })
        },
        'right': {
          header: '#js-panel-header-right',
          body: '#js-panel-body-right',
          footer: '#js-panel-footer-right',
          carousel: $('#js-panel-carousel-right').panelCarousel({
            controls : {
              container : '.js-carousel-container',
              buttonPrev: '.js-carousel-prev',
              buttonNext: '.js-carousel-next'
            },
            items: 5
          })
        }
      },
      ajaxUpdateSelectors : [],
      hidePanelButton: '#panel-hide-button',
      activeClass : 'active',
      collapseClass : 'collapsed',
      panelItemElement : 'li'
    });

    $('body').on('argillaPanelUpdated', function(event, id, response) {
      $('#' + id).find('input[type="tel"], .phone-input').each(function() {
        var $_this = $(this), val;
        $_this.mask('+7 (999) 999-99-99', {autoclear: false});
      })
    });
  });
</script>

<div class="products-fixed-panel collapsed" id="js-argilla-panel">
  <div class="products-panel-header">
    <div class="wrapper">
      <a href="" class="btn panel-link panel-fav-link fl" id="js-panel-header-left">
        <span class="icon fav-icon"></span>
        <span class="uppercase">Избранное</span> (<?php echo $this->favorite->count()?>)
        <span class="icon arrow-icon"></span>
      </a>
      <a href="" class="btn panel-link panel-basket-link fr" id="js-panel-header-right">
        <span class="icon basket-icon"></span>
        <span class="uppercase">Товаров в корзине</span> на сумму:
        <?php echo PriceHelper::price($this->basket->getSumTotal())?> руб.
        <span class="icon arrow-icon"></span>
      </a>
    </div>
  </div>
  <div class="products-panel-body">
    <div class="wrapper">
      <a href="" class="close"></a>
      <?php $this->renderPartial('/panel/_tab_favorite')?>
      <?php $this->renderPartial('/panel/_tab_basket')?>
    </div>
  </div>
</div>
