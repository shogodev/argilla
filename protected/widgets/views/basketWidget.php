<?php
/**
 * @var FBasketWidget $this
 */
?>
<div class="basket">
    <a href="<?php echo $this->url?>" title="" class="cart h3 m5">Корзина</a>
    <div class="m5 black">Товаров: <span class="purple bb"><?php echo $this->basket->createElementText('count', $this->count)?></span></div>
    <div class="m3 black">На сумму: <span class="purple bb"><?php echo $this->basket->createElementText('sum', Yii::app()->format->formatNumber($this->sum))?> руб.</span></div>
    <div class="black <?php echo $this->basket->getElementId('checkout_url')?>" <?php if( empty($this->count) ) {?>style="display: none;"<?php }?>><a class="bb" href="<?php echo $this->url?>" title="">Оформить заказ</a></div>
</div>