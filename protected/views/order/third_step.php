<?php
/**
 * @var BasketController $this
 * @var integer $orderId
 */
?>
<div class="wrapper">
  <?php $this->renderPartial('/_breadcrumbs');?>
</div>

<div class="center lightest-grey s18 pre-footer">
  <h1 class="lightest-grey s33 uppercase m35 opensans"><?php echo Yii::app()->meta->setHeader('Ваш заказ принят');?></h1>

  <?php $phonesText = ''?>
  <?php if( $this->getHeaderContacts() && $phones = $this->getHeaderContacts()->getFields('phones') ) {?>
    <?php $phonesText = '<div class="m10">Вы можете задать вопрос по телефонам:</div><div class="m20">'?>
    <?php if( isset($phones[0]) ) $phonesText .= '<div class="m5"><a class="phone-number opensans s25 bb red" href="tel:'.$phones[0]->getClearPhone().'">'.$phones[0]->value.$phones[0]->description.'</a></div>';?>
    <?php if( isset($phones[1]) ) $phonesText .= '<div class="m5"><a class="phone-number opensans s25 bb red" href="tel:'.$phones[1]->getClearPhone().'">'.$phones[1]->value.$phones[1]->description.'</a></div>';?>
    <?php $phonesText .= '</div>'?>
  <?php }?>

  <?php $defaultText = '<div class="m3">Ваш заказ был успешно принят и находится в обработке.</div>
    <div class="s24 bb white m20 opensans">Номер Вашего заказа: <span class="red">{orderId}</span></div>
    <div class="m70">
      В ближайшее время менеджер свяжется с Вами для уточнения необходимых деталей.<br />
      После этого Вы сможете оплатить заказ.<br />
      Благодарим Вас за совершение покупок на motoremont.ru
    </div>
    {phones}
    <a class="btn red-contour-btn solid-btn rounded-btn white-body-btn h34btn p10btn opensans s15 bb uppercase" href="{mainUrl}">Вернуться на главную</a>';
  ?>

  <?php echo $this->textBlockRegister('Заказ принят', $defaultText, null, array(
    '{orderId}' => $orderId,
    '{mainUrl}' => $this->createUrl('index/index'),
    '{phones}' => $phonesText,
  ))?>

</div>