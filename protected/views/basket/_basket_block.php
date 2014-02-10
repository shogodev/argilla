<?php
/**
 * @var BasketController $this
 * @var Product $data
 * @var ProductParameter|null $size
 */
?>
<tr>
  <td style="width: 265px">
    <?php if( $image = Arr::reset($data->getImages()) ) { ?>
      <a href="<?php echo $data->url?>"><img src="<?php echo $image->pre?>" alt="" /></a>
    <?php }?>
  </td>
  <td style="width: 300px">
    <div class="m15">
      <a href="<?php echo $data->url?>" class="h3 s18 bb"><?php echo $data->name?></a>
    </div>
    <table class="zero short-description-table">
      <?php if( $data->category ) {?>
        <tr>
          <th>Бренд:</th>
          <td><?php echo $data->category->name?></td>
        </tr>
      <?php }?>
      <tr>
        <th>Тип:</th>
        <td><?php echo $data->type->name?></td>
      </tr>

      <?php if( $color = $data->getProductColorParameter()  ) { ?>
        <tr>
          <td colspan="2" height="15"></td>
        </tr>
        <tr>
          <th><?php echo $color->name?>:</th>
          <td><?php echo $color->value?></td>
        </tr>
      <?php }?>

      <?php $sizeParameter = $data->getProductSizeParameter()?>
      <?php if( $sizeParameter && $sizeParameter->isAvailableValues()  ) { ?>
        <tr>
          <td colspan="2" height="15"></td>
        </tr>
        <tr>
          <th><?php echo $sizeParameter->name?>:</th>
          <?php if( $size = $data->getCollectionItems('size') ) {?>
            <td><?php echo $size->variant->name?><br /><a href="" class="chose-size-link" data-index="<?php echo $data->collectionIndex?>">Выбрать другой</a></td>
          <?php } else {?>
            <td><a href="" class="chose-size-link" data-index="<?php echo $data->collectionIndex?>">Выбрать</a></td>
          <?php }?>
        </tr>
      <?php }?>
    </table>

    <?php if( $sizeParameter ) {?>
      <div id="size-index-<?php echo $data->collectionIndex?>" style="display: none">
        <?php foreach($sizeParameter->parameters as $paramNameId => $parameter) {?>
          <a href=""
             class="size-btn select-size<?php echo !$sizeParameter->values[$paramNameId]->available ? ' disabled' : ''?><?php echo isset($size) && $size->id == $parameter->id ? ' selected' : ''?>"
             data-id="<?php echo $parameter->id?>"
             data-index="<?php echo $data->collectionIndex?>"><?php echo $sizeParameter->values[$paramNameId]?></a>
        <?php }?>
      </div>
    <?php }?>
  </td>
  <td>
    <div class="block-center"><div class="block-center-div"><div class="block-center-div-div">
          <?php if( !Utils::isDecimalEmpty($data->price_old) ) {?>
            <div class="nofloat">
              <div class="s12 center m3 old-price fr"><?php echo Yii::app()->format->formatNumber($data->price_old)?> руб.</div>
            </div>
          <?php }?>
          <div class="s28 center jurabold m3 price"><?php echo Yii::app()->format->formatNumber($data->price)?> <span class="s24 jurabold">руб.</span></div>
        </div></div></div>
  </td>
  <td>
      <span class="spinner">
        <span class="spinner-up"></span>
        <?php echo $this->basket->changeAmountInput($data, array('class' => 'inp'))?>
        <span class="spinner-down"></span>
      </span>
  </td>
  <td>
    <div class="s28 center jurabold m3 price"><?php echo Yii::app()->format->formatNumber($data->sum)?> <span class="s24 jurabold">руб.</span></div>
  </td>
  <td style="width: 59px">
    <a class="remove-from-basket" href="#" data-id="<?php echo $data->collectionExternalIndex?>"><img alt="Удалить" src="i/remove-btn.png"></a>
  </td>
</tr>