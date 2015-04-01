<?php
/**
 * @var ProductController $this
 * @var Product $data
 */
?>
<?php $this->basket->ajaxUpdate('js-wrapper-basket-header')?>

<div id="js-wrapper-basket-header">
  <?php if( !$this->basket->isEmpty() ) {?>
    <div class="hd-basket">
      <div class="basket-inner nofloat">
        <div class="basket-caption">Корзина</div>
        <div class="basket-text">
          <span class="red"><?php echo $this->basket->countAmount()?></span> <?php echo Utils::plural($this->basket->countAmount(), 'товар,товара,товаров');?>
          <span class="red"><?php echo PriceHelper::price($this->basket->getSumTotal(), ' руб.')?></span>
        </div>
      </div>
      <a href="<?php echo $this->createUrl('order/firstStep')?>" class="btn">Оформить заказ</a>
    </div>
  <?php } else {?>
    <div class="hd-basket">
      <div class="basket-inner nofloat">
        <div class="basket-caption">Корзина</div>
        <div class="basket-text">
          Ваша корзина пуста
        </div>
      </div>
    </div>
  <?php }?>
</div>