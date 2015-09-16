<?php
/**
 * @var FController $this
 */
?>
<div class="products-panel-content" id="js-panel-body-right">
  <div class="goods-carousel vitrine m15 basket-carousel menu" id="js-panel-carousel-right">
    <?php
      $this->widget('FListView', array(
        'id' => 'list-view-basket-panel',
        'htmlOptions' => array('class' => 'carousel-container js-carousel-container'),
        'dataProvider' => new FArrayDataProvider($this->basket, array('pagination' => false)),
        'itemsTagName' => 'ul',
        'itemsCssClass' => 'carousel',
        'itemView' =>  '/panel/_item',
        'skin' => array('collection' => $this->basket),
        'emptyText' => 'Корзина пуста',
      ));
    ?>
    <a href="#" class="carousel-prev js-carousel-prev carousel-controls">
      <i class="icon left-darkgrey-icon"></i>
    </a>
    <a href="#" class="carousel-next js-carousel-next carousel-controls">
      <i class="icon right-darkgrey-icon"></i>
    </a>
  </div>

  <div class="products-panel-bottom nofloat" id="js-panel-footer-right">
    <div class="fl">
      <span class="s18">В вашем заказе <?php echo $this->basket->countAmount()?> <?php echo Utils::plural($this->basket->countAmount(), 'товар,товара,товаров')?> на сумму <?php echo PriceHelper::price($this->basket->getSumTotal(), ' руб.')?></span>
    </div>
    <div class="fr">
      <a href="<?php echo $this->createUrl('order/firstStep')?>" class="btn to-order-btn golden-btn h32-btn s15 bb">Оформить заказ</a>
    </div>
    <div class="fr products-panel-one-click">
      <div class="form-row">
        <div class="form-label uppercase">
          <label for="phone100">Телефон</label>
        </div>
        <div class="form-field">
          <input type="text" name="" id="phone100" class="inp tel-inp panel-fast-order-phone" value="<?php echo Yii::app()->user->profile->phone?>">
        </div>
        <a href="" class="btn black-btn h24-btn transform-none fr white panel-fast-order">купить в 1 клик</a>
      </div>
    </div>
  </div>
</div>