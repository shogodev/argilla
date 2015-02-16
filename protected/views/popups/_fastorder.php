<?php
/**
 * @var FController $this
 */
?>
<?php $this->basket->beginFastOrderPopup(array('class' => 'one-click-popup popup'))?>
  <noindex>
    <div class="popup-header">
      <i class="icon"></i>
      <div class="popup-title center uppercase bb s22">
        форма быстрого заказа
      </div>
      <?php echo $this->basket->buttonFastOrderClose(array('class' => 'close'))?>
    </div>

    <div class="popup-body">
        <?php $this->basket->beginTemplate()?>
        <div class="product-image fl">
          <a href="{url}"><img src="{img}" alt=""></a>
        </div>
        <div class="product-name nova m10">
          <a href="{sectionUrl}" class="dark-grey s13">{sectionName}</a>
          <a class="dark-grey uppercase bb" href="{url}">{name}</a>
        </div>
      <?php $this->basket->endTemplate()?>
      <div id="<?php echo $this->basket->fastOrderFormSuccessId?>" style="display: none">
        <?php echo $this->textBlockRegister('Успешный быстрый заказ', 'Заказ успешно отправлен')?>
      </div>
      <?php echo $this->fastOrderForm?>
    </div>
  </noindex>
<?php $this->basket->endFastOrderPopup()?>
