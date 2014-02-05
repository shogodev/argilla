<?php
/**
 * @var CompareController $this
 * @var ProductParameterName[] $parametersCompare
 * @var ProductSection|FCollectionElement $group
 * @var Product[] $products
 * @var ProductSection|FCollectionElement $selectedSection
 */
?>
<div class="red-skew-block page-subcaption">
  <div class="wrapper">
    <?php $this->renderPartial('/_breadcrumbs')?>
    <h1 class="h2 white s40"><?php echo $this->clip('h1', 'Сравнение')?></h1>
  </div>
</div>

<div class="red-backskew-end" id="compare-block">
  <?php $this->compare->ajaxUpdate('compare-block')?>
  <div class="compare-block">
    <div class="wrapper nofloat">
      <?php if( isset($selectedSection) ) {?>
        <div class="compare-tabs" id="compare-tabs">
          <ul>
            <?php foreach($this->compare->getGroups() as $group) {?>
              <li><?php echo $this->compare->buttonChangeTab(
                  $group->name.' ('.$this->compare->countAmountByGroup($group->id).')',
                  $group->id,
                  array('class' => $selectedSection->id == $group->id ? 'active' : '')
                )?></li>
            <?php }?>
          </ul>

          <div class="compare-tab">
            <div class="fl" style="width: 260px">
              <table class="zero compare-table compare-table-params">
                <tr>
                  <th></th>
                </tr>
                <?php $st = 0?>
                <?php foreach($parametersCompare as $parameterCompare) {?>
                  <tr <?php if( $st++%2 == 0 ) {?>class="even"<?php }?>>
                    <th><?php echo $parameterCompare->name?></th>
                  </tr>
                <?php }?>
              </table>
            </div>

            <div class="fl scrollpane" style="width: 993px">
              <table class="zero compare-table compare-table-body">
                <tr>
                  <?php foreach($products as $product) {?>
                    <td>
                      <div class="vitrine">
                        <div class="product">
                          <?php echo $this->compare->removeButton($product, '<img alt="Удалить" src="i/remove-btn.png">', array('class' => 'remove'))?>
                          <div class="product-image m10">
                            <?php if( $image = Arr::reset($product->getImages()) ) {?>
                              <a href="<?php echo $product->url?>"><img src="<?php echo $image->pre?>" alt="" class="animate-product-image"/></a>
                            <?php }?>
                          </div>
                          <a href="<?php echo $product->url?>" class="product-name center s20 m15 jurabold"><?php echo $product->name?></a>
                          <?php if( $product->economy ) {?>
                            <div class="economy center uppercase s17 m15 bb">Экономия <?php echo $product->economy?>%</div>
                            <div class="s17 center m10 old-price"><?php echo Yii::app()->format->formatNumber($product->price_old)?> руб.</div>
                          <?php }?>
                          <div class="s29 center jurabold m10 price"><?php echo Yii::app()->format->formatNumber($product->price)?> руб.</div>
                            <?php echo $this->basket->addButtonModel('Купить', $product, array('class' => 'btn red-btn wide-btn'), array(), false)?>
                        </div>
                      </div>
                    </td>
                  <?php }?>
                </tr>
                <?php $st = 0?>
                <?php foreach($parametersCompare as $parameterCompare) {?>
                  <tr <?php if( $st++%2 == 0 ) {?>class="even"<?php }?>>
                    <?php foreach($products as $product) {?>
                      <td>
                        <?php foreach($product->parameters as $productParameter) {?>
                          <?php if( $parameterCompare->id !== $productParameter->id ) continue;?>
                            <?php echo isset($productParameter) && !empty($productParameter->value) ? $productParameter->value : ''?>
                        <?php }?>
                      </td>
                    <?php }?>
                  </tr>
                <?php }?>
              </table>
            </div>
          </div>

        </div>
      <?php } else echo $this->textBlockRegister('Нет товаров в сравнении', 'Нет товаров для сравнения', array('class' => 'center jurabold s22', 'style' => 'margin-top: 32px'));?>
    </div>
  </div>
</div>

<script id="compare-block-script">
  <?php $this->compare->ajaxUpdate('compare-block-script')?>
  //<![CDATA[
  $(function(){
    $('.compare-tab').each(function(tabIndex){
      var self = $(this);
      self.find('.compare-table-params tr').each(function(index){
        var maxHeight = $(this).find('th').height(),
          rowHeight = self.find('.compare-table-body tr:eq(' + index + ') td').height();
        // Определяем для каждой строки в каждой таблице, что выше - заголовок или сама строка, выставляем большее значение
        if ( rowHeight > maxHeight ) maxHeight = rowHeight;
        self.find('.compare-table').each(function(){
          $(this).find('tr:eq(' + index + ')').find('td, th').css('height', maxHeight);
        });
      });
      // Навешиваем скроллбар
      self.find('.scrollpane').jScrollPane({showArrows: true});
    });
  });
  //]]>
</script>