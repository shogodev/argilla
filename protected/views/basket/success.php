<?php
/**
 * @var BasketController $this
 * @var integer $orderId
 */
?>
<div class="wrapper nofloat">
  <section id="main" class="wide">
    <div class="center">
      <h1><?php echo Yii::app()->meta->setHeader('Ваш заказ принят');?></h1>
      <?php $phonesText = ''?>
      <?php if( $phones = $this->getHeaderContacts()->getFields('phones') ) {?>
        <?php if( isset($phones[0]) ) $phonesText .= '<div class="m5"><a class="phone-number s27 bb" href="tel:'.$phones[0]->getClearPhone().'">'.$phones[0]->value.$phones[0]->description.'</a> (Москва)</div>';?>
        <?php if( isset($phones[1]) ) $phonesText .= '<div class="m5"><a class="phone-number s27 bb" href="tel:'.$phones[1]->getClearPhone().'">'.$phones[1]->value.$phones[1]->description.'</a> (Краснодар)</div>';?>
      <?php }?>
      <?php $defaultText = '<div class="m20">Ваш заказ был успешно принят и находится в обработке.</div>
        <div class="s18 bb m20">Номер Вашего заказа: {orderId}</div>
        <div class="m20">
          В ближайшее время менеджер свяжется с Вами для уточнения необходимых нюансов,<br />
          после чего Вы сможете оплатить заказ.
        </div>
        <div class="m50">
          Благодарим Вас за совершение покупок на Roomelectro.ru!
        </div>
        <div class="s18 bb italic m10">Вы можете задать вопрос по телефонам:</div>
        <div class="m50">
          {phones}
        </div>
        <a class="btn green-btn h30btn p25btn s16 uppercase bb" href="{mainUrl}">Вернуться на главную</a>';
      ?>

      <?php echo $this->textBlockRegister('Заказ принят', $defaultText, null, array(
        '{orderId}' => $orderId,
        '{mainUrl}' => $this->createUrl('index/index'),
        '{phones}' => $phonesText,
      ))?>
    </div>
  </section>
</div>