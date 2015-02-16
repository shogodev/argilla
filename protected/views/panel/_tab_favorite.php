<?php
/**
 * @var FController $this
 */
?>
<div class="products-panel-content" id="js-panel-body-left">
  <div class="goods-carousel vitrine m15 fav-carousel menu" id="js-panel-carousel-left">
    <?php
      $this->widget('FListView', array(
        'id' => 'list-view-favorite-panel',
        'htmlOptions' => array('class' => 'carousel-container js-carousel-container'),
        'dataProvider' => new FArrayDataProvider($this->favorite, array('pagination' => false)),
        'itemsTagName' => 'ul',
        'itemsCssClass' => 'carousel',
        'itemView' =>  '/panel/_item',
        'skin' => array('collection' => $this->favorite),
        'emptyText' => '',
      ));
    ?>
    <a href="#" class="carousel-prev js-carousel-prev carousel-controls">
      <i class="icon left-darkgrey-icon"></i>
    </a>
    <a href="#" class="carousel-next js-carousel-next carousel-controls">
      <i class="icon right-darkgrey-icon"></i>
    </a>
  </div>

  <div class="products-panel-bottom nofloat" id="js-panel-footer-left">
    <div class="fl">
      <span class="s18">В избранном <?php echo $this->favorite->countAmount()?> <?php echo Utils::plural($this->favorite->countAmount(), 'товар,товара,товаров')?> на сумму <?php echo PriceHelper::price($this->favorite->getSumTotal(), ' руб.')?></span>
    </div>
    <div class="fr">
      <?php echo $this->favorite->ajaxUpdate('header-basket-block')->buttonMergeWithBasket('Добавить все в заказ', array('class' => 'btn to-order-btn golden-btn h32-btn s15 bb'))?>
    </div>
  </div>
</div>