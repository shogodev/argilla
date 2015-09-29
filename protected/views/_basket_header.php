<?php
/**
 * @var ProductController $this
 * @var Product $data
 */
?>
<?php
$this->basket->addAfterAjaxScript(new CJavaScriptExpression("
  if (action == 'add') {
    var parent = element.closest('#add-through-popup').data('parent');
    if (parent instanceof Object) {
      image = parent.find('.js-animate-image');
    } else {
      image = element.closest('.product, .js-card-animate-parent').find('.js-animate-image');
    }
    $('.js-animate').addInCollection(image);
  }
"));?>

<?php $this->basket->ajaxUpdate('js-wrapper-basket-header')?>

<div class="header--basket" id="js-wrapper-basket-header">
  <?php if( !$this->basket->isEmpty() ) {?>
    <div class="js-animate">
      <a href="<?php echo $this->createUrl('order/firstStep')?>">
        Корзина <br>
        <?php echo $this->basket->countAmount()?> <?php echo Utils::plural($this->basket->countAmount(), 'товар,товара,товаров');?>
        <?php echo PriceHelper::price($this->basket->getSumTotal(), ' руб.')?>
      </a>
      <a href="<?php echo $this->createUrl('order/firstStep')?>">
        Оформить заказ
      </a>
    </div>
  <?php } else {?>
    <div class="js-animate">
      Корзина <br>
      Ваша корзина пуста
    </div>
  <?php }?>
</div>