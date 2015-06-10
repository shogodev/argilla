<?php
/**
 * @var FController $this
 */
?>
<?php $this->basket->beginFastOrderPopup(array('class' => 'one-click-popup popup'))?>
  <!--noindex-->
    <a href="" class="close"></a>
    <div class="s19 uppercase m15">Купить в 1 клик</div>

    <?php $this->basket->beginTemplate()?>
      <div class="fast-order-info m30">
        <div class="fast-order-pic">
          <a href="{url}"><img src="{img}" alt="" /></a>
        </div>
        <div class="fast-order-name"><a href="{url}">{name}</a></div>
        <div class="fast-order-price">{price}</div>
      </div>
    <?php $this->basket->endTemplate()?>

    <div id="<?php echo $this->basket->fastOrderFormSuccessId?>" style="display: none">
      <?php echo $this->textBlockRegister('Успешный быстрый заказ', 'Заказ успешно отправлен')?>
    </div>
    <?php echo $this->fastOrderForm?>
  <!--/noindex-->
<?php $this->basket->endFastOrderPopup()?>