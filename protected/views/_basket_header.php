<?php
/**
 * @var ProductController $this
 * @var Product $data
 */
?>
<?php $this->basket->ajaxUpdate('basket-header-block')?>

<div id="basket-header-block">
  <?php if( !$this->basket->isEmpty() ) {?>
    <div class="hd-basket">
      <div class="bb uppercase">Корзина</div>
      <div class="italic m5"><?php echo Utils::plural($this->basket->countAmount(), 'товар,товара,товаров');?> <?php echo $this->basket->countAmount()?></div>
      <div class="italic"><span class="bb"><?php echo PriceHelper::price($this->basket->getSumTotal())?></span> руб.</div>
      <a href="<?php echo $this->createUrl('basket/index')?>" class="btn order-btn">Оформить заказ</a>
    </div>
  <?php } else {?>
    <div class="hd-basket empty">
      <div class="bb uppercase">Корзина</div>
      <div class="italic">пусто</div>
    </div>
  <?php }?>
</div>