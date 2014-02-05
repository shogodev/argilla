<?php
/**
 * @var FController $this
 */
?>
<div id="favorites-block">
  <div class="goods-carousel vitrine m15" id="seen-goods-carousel">
  <?php
  $this->widget('FListView', array(
    'id' => 'list-view-favorite',
    'dataProvider' => new FArrayDataProvider($this->visits, array('pagination' => false)),
    'itemsTagName' => 'ul',
    'itemsCssClass' => 'carousel',
    'htmlOptions' => array('class' => 'favorite-list carousel-container'),
    'itemView' =>  '/panel/_item',
    'skin' => 'favorite',
    'emptyText' => '',
  ));?>
    <a href="#" class="carousel-controls carousel-prev disabled"></a>
    <a href="#" class="carousel-controls carousel-next"></a>
  </div>
  <script>
    //<![CDATA[
    $(function() {
      var container = '#seen-goods-carousel';
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
</div>