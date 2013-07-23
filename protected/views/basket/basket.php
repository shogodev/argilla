<?php
/**
 * @var BasketController $this
 * @var Service $service
 * @var Product $product
 */
?>
<div id="content" class="paddings">
<?php $this->renderPartial('/breadcrumbs');?>

<div class="nofloat" id="top_block">
  <h1 class="left"><?php echo $this->clip('h1', 'Корзина')?></h1>

  <?php if( !$this->basket->isEmpty() ) {?>
    <div class="right">
      <span class="btn black-btn basket-step">Шаг 1: Выбор товаров</span>
      <a href="<?php echo $this->createUrl('basket/checkout')?>" class="btn grey-btn basket-step">Шаг 2: Выбор метода доставки и оплаты</a>
    </div>
  <?php }?>
</div>

<?php
  $this->widget('FListView', array(
    'template' => '{items}',
    'dataProvider' => new FArrayDataProvider($this->basket, array('pagination' => false)),
    'htmlOptions' => array('class' => 'basket-list'),
    'itemView' =>  'basket_block',
    'ajaxUpdate' => 'top_block, bottom_block',
    'ajaxVar' => null,
    'ajaxType' => 'post',
    'enableHistory' => false,
    'emptyText' => 'Корзина пуста'
));?>

<div id="bottom_block">
<?php if( !$this->basket->isEmpty() ) {?>
  <table id="basket-total-table" class="zero m20">
    <tr>
      <td style="width: 420px"></td>
      <td class="h2" style="width: 420px">Стоимость выбранных услуг:</td>
      <td>
        <span class="bb s30"><?php echo Yii::app()->format->formatNumber($this->basket->serviceSum())?></span>
        <span class="s18">руб.</span>
      </td>
    </tr>
    <tr>
      <td style="width: 420px"></td>
      <td class="h2" style="width: 420px">Стоимость выбранных товаров и услуг:</td>
      <td>
        <span class="bb s30"><?php echo Yii::app()->format->formatNumber($this->basket->totalSum())?></span>
        <span class="s18">руб.</span>
      </td>
    </tr>
  </table>

  <div class="nofloat m20">
    <a href="" class="btn grey-btn left" id="return-btn">Продолжить покупки</a>
    <div class="right">
          <a href="<?php echo $this->createUrl('basket/checkout')?>" class="btn green-btn">Оформить заказ</a>
    </div>
  </div>
  <div class="nofloat m10">
    <div class="right">
      <span class="btn black-btn basket-step">Шаг 1: Выбор товаров</span>
      <a href="<?php echo $this->createUrl('basket/checkout')?>" class="btn grey-btn basket-step">Шаг 2: Выбор метода доставки и оплаты</a>
    </div>
  </div>
<?php }?>

  <script type="text/javascript">
    //<![CDATA[
    $(function() {
      $('#return-btn').on('click', function(e){
        e.preventDefault();
        history.back();
      });
    });
    //]]>
  </script>
</div>
</div>