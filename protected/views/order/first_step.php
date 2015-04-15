<?php
/**
 * @var BasketController $this
 * @var array $_data_
 */
?>
<div class="wrapper">
  <?php $this->renderOverride('_breadcrumbs');?>
</div>

<div class="white-body pre-footer">
  <div class="wrapper">
    <div class="nofloat m5">
      <h1 class="uppercase s33 fl"><?php echo Yii::app()->meta->setHeader('Корзина')?></h1>
      <div class="basket-steps">
        <span class="step active">Шаг 1: Выбор товаров</span>
        <a href="<?php echo $this->createUrl('order/secondStep')?>" class="step">Шаг 2: Выбор метода доставки и оплаты</a>
      </div>
    </div>

    <?php
      $this->widget('FListView', array(
        'tagName' => null,
        'itemView' => 'products/_product_block',
        'template' => $this->renderPartial('products/_products', $_data_, true),
        'dataProvider'=> new FArrayDataProvider($this->basket, array('pagination' => false)),
      ));
    ?>

    <div class="nofloat basket-buttons">
      <a href="<?php echo $this->createUrl('order/secondStep')?>" class="right btn red-contour-btn rounded-btn h34btn opensans s15 bb uppercase">Оформить заказ</a>
      <?php $this->widget('ReturnButtonWidget', array('text' => 'Продолжить покупки', 'htmlOptions' => array('class' => 'right btn blue-contour-btn rounded-btn h34btn opensans s15 bb uppercase')))?>
    </div>
  </div>
</div>
