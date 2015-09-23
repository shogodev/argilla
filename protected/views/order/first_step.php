<?php
/**
 * @var BasketController $this
 * @var array $_data_
 */
?>

<?php $this->renderOverride('_breadcrumbs');?>

<h1>
  <?php echo Yii::app()->meta->setHeader('Корзина')?>
</h1>

<ul>
  <li>
    <span>Шаг 1: Выбор товаров</span>
  </li>
  <li>
    <a href="<?php echo $this->createUrl('order/secondStep')?>">
      Шаг 2: Выбор метода доставки и оплаты
    </a>
  </li>
</ul>

<?php
  $this->widget('FListView', array(
    'tagName' => null,
    'itemView' => 'products/_product_block',
    'template' => $this->renderPartial('products/_products', $_data_, true),
    'dataProvider'=> new FArrayDataProvider($this->basket, array('pagination' => false)),
  ));
?>

<a href="<?php echo $this->createUrl('order/secondStep')?>">
  Оформить заказ
</a>

<?php $this->widget('ReturnButtonWidget', array(
  'text' => 'Продолжить покупки',
  'htmlOptions' => array('class' => '')
))?>
