<?php
/**
 * @var ProductController $this
 * @var Product $model
 */
?>

<div class="product-gallery">
  <?php if( $images = $model->getImages('gallery') ) { ?>
  <div id="product-carousel" class="product-carousel">
    <div class="carousel-container carousel-vertical">
      <ul class="carousel">
        <?php foreach($images as $image) { ?>
        <li>
          <a href="<?php echo $image?>" class="js-pswp-gallery-item">
            <img src="<?php echo $image->pre?>" alt="" />
          </a>
        </li>
        <?php } ?>
      </ul>
    </div>
    <span class="carousel-controls carousel-prev"></span>
    <span class="carousel-controls carousel-next"></span>
  </div>
  <?php } ?>

  <script>
    $(function() {
      var container = $('#product-carousel');
      if ( $('.carousel li', container).length > 3 ) {
        $('.carousel-container', container).jcarousel({
          'animation' : 'fast',
          'vertical'  : true
        });
        $('.carousel-next, .carousel-prev', container).show()
          .on('jcarouselcontrol:active', function() {
            $(this).removeClass('disabled');
          })
          .on('jcarouselcontrol:inactive', function() {
            $(this).addClass('disabled');
          });
        $('.carousel-prev', container).jcarouselControl({
          target: '-=1'
        });
        $('.carousel-next', container).jcarouselControl({
          target: '+=1'
        });
        if ( isMobile ) {
          $(container).on('swipeup swipesown', function(e) {
            if ( (e.velocityY > 0.1) || (e.velocityY < -0.1) ) {
              if ( e.type == 'swipedown' ) {
                $('.carousel-container', container).jcarousel('scroll', '-=1');
              } else if ( e.type == 'swipeup' ) {
                $('.carousel-container', container).jcarousel('scroll', '+=1');
              }
            }
          });
        }
      }
    });
  </script>
</div>

<div class="product-main-image">
  <?php if( $image = $model->getImage() ) { ?>
  <a href="<?php echo $image?>" class="js-pswp-gallery-item">
    <img src="<?php echo $image->big?>" alt="<?php echo $model->getHeader()?>" class="animate-image" />
  </a>
  <?php } ?>
  <div class="product-icons">
    <?php if( $model->discount ) { ?>
    <span class="discount">24%</span>
    <?php } ?>
    <?php if( $model->spec ) { ?>
    <span class="leader">ХИТ</span>
    <?php } ?>
    <?php if( $model->novelty ) { ?>
      <span class="new">NEW</span>
    <?php } ?>
  </div>
</div>