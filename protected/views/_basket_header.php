<?php
/**
 * @var ProductController $this
 * @var Product $data
 */
?>
<?php
$this->basket->addAfterAjaxScript(new CJavaScriptExpression("
   if( action == 'add' )
   {
     var parent = element.closest('#add-through-popup').data('parent');

     if( parent instanceof Object )
      image = parent.find('.animate-image');
     else
      image = element.closest('.product, .js-card-animate-parent').find('.animate-image');

     $('.js-animate').addInCollection(image);
   }
  "));
?>
<?php $this->basket->ajaxUpdate('js-wrapper-basket-header')?>

<div id="js-wrapper-basket-header">
  <?php if( !$this->basket->isEmpty() ) {?>
    <div class="hd-basket js-animate">
      <a href="<?php echo $this->createUrl('order/firstStep')?>" class="basket-inner nofloat">
        <div class="basket-caption">Корзина</div>
        <div class="basket-text">
          <span class="red"><?php echo $this->basket->countAmount()?></span> <?php echo Utils::plural($this->basket->countAmount(), 'товар,товара,товаров');?>
          <span class="red"><?php echo PriceHelper::price($this->basket->getSumTotal(), ' руб.')?></span>
        </div>
      </a>
      <a href="<?php echo $this->createUrl('order/firstStep')?>" class="btn">Оформить заказ</a>
    </div>
  <?php } else {?>
    <div class="hd-basket js-animate">
      <div class="basket-inner nofloat">
        <div class="basket-caption">Корзина</div>
        <div class="basket-text">
          Ваша корзина пуста
        </div>
      </div>
    </div>
  <?php }?>
</div>