<?php
/**
 * @var BasketController $this
 * @var array $_data_
 */
?>

<?php $this->basket->ajaxUpdate(array('basket-top-block', 'basket-products-block', 'basket-bottom-block'))?>

<div class="wrapper">

  <?php $this->renderOverride('_breadcrumbs');?>

  <div class="nofloat m15" id="basket-top-block">
    <h1 class="fl"><?php echo Yii::app()->meta->setHeader('Корзина')?></h1>

    <?php if( !$this->basket->isEmpty() ) { ?>
    <div class="basket-steps fr">
      <span class="step active">Шаг 1: выбор товара</span>
      <a href="<?php echo $this->createUrl('basket/checkout')?>" class="step">Шаг 2: выбор метода оплаты и доставки</a>
    </div>
    <?php }?>
  </div>

  <div id="basket-products-block">
    <?php if( !$this->basket->isEmpty() ) { ?>
      <table class="zero basket-table m15">
        <tr>
          <th>Товар</th>
          <th>Цена</th>
          <th>Кол-во</th>
          <th>Стоимость</th>
          <th></th>
        </tr>
        <tr><td colspan="5"></td></tr>

        <?php foreach($this->basket as $key => $product) { ?>
          <?php $this->renderPartial('_basket_block', array('index' => $key, 'data' => $product))?>
        <?php } ?>

        <tr class="product-row persons-row">
          <td colspan="3">
            <span class="s24">Вся посуда предоставляется количеству указанных вами персон</span>
          </td>
          <td colspan="2">
            <span class="grey s15">Количество персон:</span>
                <span class="spinner">
                  <span class="spinner-up"></span>
                  <input type="text" class="inp" value="60" />
                  <span class="spinner-down"></span>
                </span>
          </td>
        </tr>
      </table>
      <script language="JavaScript">
        $('select').sSelect();
        $('.product-row .product-option').on('change', function() {
          var options = {};
          var parent = $(this).closest('.product-row');

          parent.find('.product-option').each(function(key) {
            options[key] = {'type' : 'productOption', 'id' : $(this).val()}
          });

          var data = {
            'index' : parent.data('index'),
            'options' : options
          };

          $.fn.collection('<?php echo $this->basket->keyCollection?>').send({
            'action' : 'changeOptions',
            'data' : data
          });
        });
      </script>


      <div class="nofloat m10">
        <div class="fr s24">
          Стоимость выбранных товаров: <span class="s34 bb"><?php echo PriceHelper::price($this->basket->getSumTotal(), ' руб.')?></span>
        </div>
      </div>

      <div class="nofloat m15">
        <?php $this->widget('ReturnButtonWidget', array('text' => 'Продолжить покупки', 'htmlOptions' => array('class' => 'btn green-contour-btn h47btn s15 uppercase left')))?>
        <a href="<?php echo $this->createUrl('basket/checkout')?>" class="btn green-btn h47btn s30 bb right">Заказать</a>
      </div>
    <?php }?>
  </div>

  <div id="basket-bottom-block">
    <?php if( !$this->basket->isEmpty() ) { ?>

      <div class="basket-steps m15">
        <span class="step active">Шаг 1: выбор товара</span>
        <a href="<?php echo $this->createUrl('basket/checkout')?>" class="step">Шаг 2: выбор метода оплаты и доставки</a>
      </div>

    <?php } else { ?>
      <?php echo $this->textBlockRegister('Корзина пуста', 'Корзина пуста')?>
    <?php } ?>
  </div>

</div>
