<?php
/**
 * @var FController $this
 */
?>
<?php $this->basket->beginFastOrderPopup(array('class' => 'popup one-click-popup'))?>
  <noindex>
    <a href="" class="close"></a>
    <div class="nofloat">
      <?php $this->basket->beginTemplate(array('class' => 'left-block'))?>
        <a href="{url}" class="product-name m5">{name}</a>
        <div class="product-image m15">
          <a href="{url}">
            <img src="{img}" alt="" />
          </a>
        </div>
      <?php $this->basket->endTemplate()?>

      <div class="right-block">
        <div class="h2 s15">Форма быстрого заказа</div>
        <div id="<?php echo $this->basket->fastOrderFormSuccessId?>" style="display: none">
          <?php echo $this->textBlockRegister('Успешный быстрый заказ', 'Заказ успешно отправлен')?>
        </div>
        <?php echo $this->fastOrderForm?>
      </div>
    </div>
  </noindex>
<?php $this->basket->endFastOrderPopup()?>