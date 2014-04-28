<?php
/**
 * @var BasketController $this
 * @var array $_data_
 */
?>

<?php $this->basket->ajaxUpdate(array('basket-top-block', 'basket-products-block', 'basket-bottom-block'))?>

<div class="wrapper">

  <?php $this->renderOverride('_breadcrumbs');?>

  <div class="nofloat m20">

    <h1 class="fl"><?php echo $this->clip('h1', 'Корзина')?></h1>

    <div id="basket-top-block">
      <?php if( !$this->basket->isEmpty() ) { ?>
      <div class="fr">
        <span class="basket-step current">Шаг 1: выбор товаров</span>
        <a href="<?php echo $this->createUrl('basket/checkout')?>" class="basket-step">Шаг 2: выбор метода доставки и оплаты</a>
      </div>
      <?php } ?>
    </div>
  </div>

  <div id="basket-products-block">

    <?php if( !$this->basket->isEmpty() ) { ?>
    <table class="zero basket-table m10">
    <colgroup>
      <col style="width: 40%">
      <col style="width: 19%">
      <col style="width: 14%">
      <col style="width: 18%">
      <col style="width: 9%">
    </colgroup>
    <?php foreach($this->basket as $key => $product) { ?>
      <?php $this->renderPartial('_basket_block', array('index' => $key, 'data' => $product))?>
    <?php } ?>
    </table>

    <table class="zero basket-summary-table m30">
      <colgroup>
        <col style="width: 78%">
        <col style="width: auto">
      </colgroup>
      <tr>
        <td>Стоимость выбранных товаров:</td>
        <td>
          <div class="s14 uppercase">
            <span class="price s30 bb black"><?php echo Yii::app()->format->formatNumber($this->basket->totalSum())?></span> руб.
          </div>
        </td>
      </tr>
    </table>

    <div class="popup confirm-popup" id="remove-from-basket-popup">
      <a href="" class="close"></a>
      <div class="center m25 jurabold">
        Товар будет удален из Корзины.<br />
        Вы уверены?
      </div>
      <div class="nofloat s0">
        <a href="" class="btn dark-btn halfsize-btn ok-btn remove-basket">Подтвердить</a>
        <a href="" class="btn red-btn halfsize-btn cancel-btn">Отменить</a>
      </div>
    </div>

    <script>
      //<![CDATA[
      $(function(){
        $('.remove-from-basket').click(function(e){
          e.preventDefault();
          var target = $('#remove-from-basket-popup');
          $('#remove-from-basket-popup .ok-btn').data($(this).data());
          $.overlayLoader(true, target);
        });
        $('#remove-from-basket-popup .cancel-btn, #remove-from-basket-popup .ok-btn').click(function(e){
          e.preventDefault();
          var target = $(this).closest('.popup');
          $.overlayLoader(false, target);
        });
      });
      //]]>
    </script>

    <div class="nofloat m20">
      <a href="<?php echo $this->createUrl('basket/checkout')?>" class="btn orange-btn h36btn widepaddings-btn s24 right">Оформить заказ</a>
      <a href="" class="btn grey-btn h36btn widepaddings-btn s24 bb right" id="return-btn">Продолжить покупки</a>
    </div>
    <script>
      //<![CDATA[
      $('#return-btn').on('click', function(e){
        e.preventDefault();
        history.back();
      });
      //]]>
    </script>
    <?php } ?>
  </div>

  <div id="basket-bottom-block">
    <?php if( !$this->basket->isEmpty() ) { ?>

      <div class="center m50">
        <span class="basket-step current">Шаг 1: выбор товаров</span>
        <a href="<?php echo $this->createUrl('basket/checkout')?>" class="basket-step">Шаг 2: выбор метода доставки и оплаты</a>
      </div>

    <?php } else { ?>
      <?php echo $this->textBlockRegister('Корзина пуста', 'Корзина пуста')?>
    <?php } ?>
  </div>

</div>