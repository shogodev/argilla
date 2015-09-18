<?php
/**
 * @var BasketController $this
 * @var FOrderForm $form
 */
?>
<div class="wrapper">
  <?php $this->renderPartial('/_breadcrumbs');?>
</div>

<div class="white-body pre-footer">
  <div class="wrapper">
    <div class="nofloat m5">
      <h1 class="uppercase s33 fl"><?php echo Yii::app()->meta->setHeader('Корзина');?></h1>
      <div class="basket-steps">
        <a href="<?php echo $this->createUrl('order/firstStep')?>" class="step">Шаг 1: Выбор товаров</a>
        <span class="step active">Шаг 2: Выбор метода доставки и оплаты</span>
      </div>
    </div>

    <?php echo $form->renderBegin()?>

      <fieldset>
        <div class="h2">Личные данные</div>
        <?php foreach(array('name', 'phone', 'email') as $element) {?>
           <?php echo $form->renderElement($element);?>
        <?php }?>
      </fieldset>

      <fieldset>
        <div class="h2">Доставка и оплата</div>
        <?php foreach(array('delivery_type_id', 'address') as $element) {?>
          <?php echo $form->getDeliveryForm()->renderElement($element);?>
        <?php }?>

        <?php echo $form->getPaymentForm()->renderElement('payment_type_id');?>
        <div id="js-platron-block">
          <?php echo $form->getPaymentForm()->renderElement('system_payment_type_id');?>
        </div>
      </fieldset>

      <fieldset>
        <?php echo $form->renderElement('comment');?>
      </fieldset>

      <div class="form-submit">
        <div class="nofloat">
          <?php echo $form->renderButtons()?>
        </div>
      </div>

    <div class="order-form-total">
      <div class="s13 grey" id="js-delivery-block" style="display: none">
        <span class="s19 bb uppercase black">Стоимость доставки:</span>
        <span class="s22 red bb" id="js-delivery-price"></span>
      </div>
      <div class="s13 grey">
        <span class="s19 bb uppercase black">Стоимость вашей покупки:</span>
        <span class="s22 red bb" id="js-total-sum" data-sum="<?php echo floatval($this->basket->getSumTotal());?>"><?php echo PriceHelper::price($this->basket->getSumTotal(), ' руб.')?></span>
      </div>
    </div>

    <div class="selfdelivery-block" style="display: none"><!--Карта проезда--></div>

    <?php echo $form->renderEnd()?>

    <div class="nofloat">
      <div class="basket-steps m40">
        <a href="<?php echo $this->createUrl('order/firstStep')?>" class="step">Шаг 1: Выбор товаров</a>
        <span class="step active">Шаг 2: Выбор метода доставки и оплаты</span>
      </div>
    </div>
  </div>
</div>

<script>
  $(function() {
    var form = $('#<?php echo $form->getActiveFormWidget()->id?>');
    var deliverySelf = <?php echo OrderDeliveryType::SELF_DELIVERY?>;
    var deliveryRegion = <?php echo OrderDeliveryType::DELIVERY_REGION?>;
    var delivery = <?php echo CJavaScript::encode(array(
        OrderDeliveryType::DELIVERY_MOSCOW,
        OrderDeliveryType::DELIVERY_MOSCOW_REGION,
        OrderDeliveryType::DELIVERY_REGION
      ))?>;
    var deliveryPriceList = <?php echo CJSON::encode(PriceHelper::decimalToFloat(CHtml::listData(OrderDeliveryType::model()->findAll(), 'id', 'price')));?>;
    var freeDeliveryLimit = <?php echo OrderDeliveryType::FREE_DELIVERY_LIMIT?>;
    var currencySuffix = ' руб.';

    var paymentEPay = <?php echo OrderPaymentType::E_PAY?>;
    var paymentCash = <?php echo OrderPaymentType::CASH?>;
    var platronElements = $('#js-platron-block input:radio');
    var paymentMethodBlock = $('#OrderPayment_payment_type_id').closest('.form-row');
    var radioBlockContainer = 'div';

    paymentMethodBlock.find('input:radio[value=' + paymentEPay + ']').closest(radioBlockContainer).hide();

    form.relatedFields({rules : [
      {'action' : 'show', 'dest' : 'Order[address]', 'src': 'OrderDelivery[delivery_type_id]', 'srcValues' : delivery},
      {'action' : 'call', 'src': 'OrderDelivery[delivery_type_id]', 'callback' : function(element, value) {
        if( value == deliverySelf )
        {
          $('.selfdelivery-block').stop(true, true).fadeIn();
          element.closest('fieldset').addClass('with-map');
        }
        else
        {
          $('.selfdelivery-block').stop(true, true).fadeOut();
          element.closest('fieldset').removeClass('with-map');
        }
        if( value == deliveryRegion )
        {
          var paymentCashElement = paymentMethodBlock.find('input:radio[value=' + paymentCash + ']');
          paymentCashElement.prop('checked', false).change();
          paymentCashElement.closest(radioBlockContainer).hide();
        }
        else
        {
          paymentMethodBlock.find('input:radio[value=' + paymentCash + ']').closest(radioBlockContainer).show();
        }

        var setTotalPrice = function(price) {
          price = Number(price)
          if( price > 0 )
            $('#js-total-sum').text(number_format(price) + currencySuffix);
          else
            $('#js-total-sum').text('Звоните');
        };

        if( deliveryPriceList[value] && deliveryPriceList[value] > 0 )
        {
          if( freeDeliveryLimit == -1 || freeDeliveryLimit > $('#js-total-sum').data('sum') )
          {
            $('#js-delivery-price').text(number_format(deliveryPriceList[value]) + currencySuffix);
            setTotalPrice(deliveryPriceList[value] + $('#js-total-sum').data('sum'));
          }
          else
          {
            $('#js-delivery-price').text('Бесплатно');
            setTotalPrice($('#js-total-sum').data('sum'));
          }

          $('#js-delivery-block').show();
        }
        else {
          setTotalPrice($('#js-total-sum').data('sum'));
          $('#js-delivery-block').hide();
        }

      }},
      {'action' : 'call', 'src' : 'OrderPayment[payment_type_id]', 'callback' : function(element, value) {
        if( value != paymentEPay )
          platronElements.prop('checked', false).change();
      }},
      {'action' : 'call', 'src' : 'OrderPayment[system_payment_type_id]', 'callback' : function(element, value) {
        if( value !== undefined ) {
          paymentMethodBlock.find('input:radio[value=' + paymentEPay + ']').click();
        }
      }}
    ]});
  });
</script>