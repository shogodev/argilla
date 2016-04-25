<?php
/**
 * @var ProductController $this
 * @var Product $model
 */
?>

<div class="product-gallery">
  <?php if( $images = $model->getImages('gallery') ) { ?>
  <div id="product-carousel" class="product-carousel">
    <div class="carousel-container">
      <ul class="js-gallery">
        <?php foreach($images as $image) { ?>
        <li>
          <a href="<?php echo $image?>" class="js-gallery-item">
            <img src="<?php echo $image->pre?>" alt="" />
          </a>
        </li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <?php } ?>
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