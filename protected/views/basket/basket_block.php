<?php
/**
 * @var BasketController $this
 * @var Service $service
 * @var Product $data
 */
?>
<table class="basket-table zero m20">
  <tr>
    <th style="width: 500px">Товар №<?php echo $index+1?></th>
    <th>Цена</th>
    <th>Количество</th>
    <th>Стоимость</th>
    <th></th>
  </tr>
  <tr>
    <td>
    <?php if( $image = Arr::reset($data->getImages()) ) { ?>
      <div class="left">
        <a href="<?php echo $data->url?>"><img src="<?php echo $image?>" alt="" width="236" /></a>
      </div>
    <?php } ?>

    <div class="no-overflow s12">
      <div class="h2 s15"><?php echo $data->name?></div>
      <?php if( $data->getParameters() ) { ?>
          <?php foreach($data->getParameters() as $i => $parameter) { ?>
            <?php if( empty($parameter->value) ) continue;?>
            <div class="m15"><?php echo $parameter->name?>: <?php echo $parameter->value?></div>
          <?php } ?>
      <?php } ?>
    </div>

    </td>
    <td style="text-align: center">
      <div class="basket-price-block">
        <div class="nofloat">
          <?php if( !empty($data->price_old) ) {?>
            <div class="s14 fr old-price">
              <span><?php echo Yii::app()->format->formatNumber($data->price_old)?> руб.</span>
            </div>
          <?php }?>
        </div>
        <div class="fr">
          <span class="bb s30"><?php echo Yii::app()->format->formatNumber($data->price)?></span>
          <span class="s18">руб.</span>
        </div>
      </div>
    </td>
    <td style="text-align: center">
      <div class="spinner">
        <a class="spinner-up" href=""></a>
        <?php echo $this->basket->changeAmountInput($data)?>
        <a class="spinner-down" href=""></a>
      </div>
    </td>
    <td style="text-align: center">
      <span class="bb s30"><?php echo Yii::app()->format->formatNumber($data->sum)?></span>
      <span class="s18">руб.</span>
    </td>
    <td>
      <?php echo $this->basket->removeButton($data, '', array('class' => 'remove-btn'))?>
    </td>
  </tr>
  <?php if( isset($data->collectionItems['service']) && !$this->basket->isEmpty($data->collectionItems['service']) ) {?>
    <tr class="grey-body">
      <th>Услуга</th>
      <th>Цена</th>
      <th>Количество</th>
      <th>Стоимость</th>
      <th></th>
    </tr>
    <?php foreach($data->collectionItems['service'] as $service) {?>
      <tr class="grey-body s18">
        <td><?php echo $service->name?></td>
        <td class="bb" style="text-align: center"><?php echo Utils::isDecimalEmpty($service->price) ? 'Бесплатно' : Yii::app()->format->formatNumber($service->price)?> <?php echo $service->price_postfix?></td>
        <td style="text-align: center">
          <?php if( !Utils::isDecimalEmpty($service->price) ) {?>
            <div class="spinner">
              <a class="spinner-up" href=""></a>
              <?php echo $this->basket->changeAmountInput($service)?>
              <a class="spinner-down" href=""></a>
            </div>
          <?php }?>
        </td>
        <td class="bb" style="text-align: center"><?php echo Yii::app()->format->formatNumber($service->sum)?> <?php echo $service->price_postfix?></td>
        <td>
          <?php echo $this->basket->removeButton($service, '', array('class' => 'remove-btn'))?>
        </td>
      </tr>
    <?php }?>
  <?php }?>
</table>

<?php if($index < $data->parentCollection->count() - 1 ) {?>
  <div class="hr1 m0"></div>
<?php }?>