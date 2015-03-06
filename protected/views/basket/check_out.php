<?php
/**
 * @var BasketController $this
 * @var FForm $form
 */
?>
<div class="wrapper nofloat">
  <section id="main" class="wide">
    <div class="basket-steps m35">
      <h1 class="home"><?php echo Yii::app()->meta->setHeader('Корзина');?></h1>
      <a href="<?php echo $this->createUrl('basket/index')?>">Шаг 1. Выбор товара</a>
      <div class="active-step"><span>Шаг 2. Выбор способа оплаты и доставки</span></div>
    </div>

    <?php echo $form?>
  </section>
</div>

<script>
  $(function() {
    var form = $('#<?php echo $form->getActiveFormWidget()->id?>');
    var deliverySelf = <?php echo OrderDeliveryType::SELF_DELIVERY?>;
    var delivery = <?php echo CJavaScript::encode(array(
        OrderDeliveryType::DELIVERY_MOSCOW,
        OrderDeliveryType::DELIVERY_MOSCOW_REGION,
        OrderDeliveryType::DELIVERY_REGION
      ))?>;

    var paymentEPay = <?php echo OrderPaymentType::E_PAY?>;
    var platronElements = $('#platron-block input:radio');
    var paymentMethodBlock = $('#Order_payment_id').closest('.form-row');
    var radioBlockContainer = 'div';

    paymentMethodBlock.find('input:radio[value=' + paymentEPay + ']').closest(radioBlockContainer).hide();

    form.relatedFields({rules : [
      {'action' : 'show', 'dest' : 'Order[address]', 'src': 'Order[delivery_id]', 'srcValues' : delivery},
      {'action' : 'call', 'src': 'Order[delivery_id]', 'callback' : function(element, value) {
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
      }},
      {'action' : 'call', 'src' : 'Order[payment_id]', 'callback' : function(element, value) {
        if( value != paymentEPay )
          platronElements.prop('checked', false).change();
      }},
      {'action' : 'call', 'src' : 'OrderPayment[payment_type_id]', 'callback' : function(element, value) {
        if( value !== undefined ) {
          paymentMethodBlock.find('input:radio[value=' + paymentEPay + ']').click();
        }
      }}
    ]});
  });
</script>