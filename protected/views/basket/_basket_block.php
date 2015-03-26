<?php
/**
 * @var BasketController $this
 * @var Product $data
 * @var ProductParameter|null $size
 */
?>
<tr class="product-row" data-index="<?php echo $data->collectionIndex?>">
  <td>
    <?php if( $image = Arr::reset($data->getImages()) ) { ?>
      <a href="<?php echo $data->getUrl()?>" class="product-pic"><img src="<?php echo $image->pre?>" alt="" /></a>
    <?php }?>

    <div class="product-info">
      <div class="m20 nova">
        <a href="<?php echo $data->getUrl()?>" class="s24 uppercase"><?php echo $data->name?></a>
      </div>
      <?php if( $optionGroup = $data->getOptionGroup() ) {?>
        <table class="zero product-params-choice-table">
          <?php foreach($optionGroup as $group) {?>
            <tr>
              <td class="grey"><?php echo $group->name?>:</td>
              <td>
                <?php $group->renderOptions(array('class' => 'product-option'))?>
              </td>
            </tr>
          <?php }?>
        </table>
      <?php }?>
      <?php if( $ingredients = $data->getCollectionItems('ingredients') ) {?>
        <?php foreach($ingredients as $ingredient) {?>
          <?php echo $ingredient->name?> <?php echo $ingredient->collectionAmount?><br/>
        <?php }?>
      <?php }?>
    </div>
  </td>
  <td>
    <span class="s34 bb nowrap"><?php echo PriceHelper::price($data->getPrice(), ' руб.')?></span>
  </td>
  <td>
        <span class="spinner">
          <span class="spinner-down"></span>
          <?php echo $this->basket->inputAmountCollection($data, array('class' => 'inp')) ?>
          <span class="spinner-up"></span>
        </span>
  </td>
  <td>
    <span class="s34 bb nowrap"><?php echo PriceHelper::price($data->sum, ' руб.')?></span>
  </td>
  <td>
    <?php echo $this->basket->buttonRemove($data, '', array('class' => 'remove'))?>
  </td>
</tr>
<tr class="spacer"><td colspan="5"></td></tr>