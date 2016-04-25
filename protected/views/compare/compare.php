<?php
/**
 * @var CompareController $this
 * @var ProductParameterName[] $parametersCompare
 * @var ProductSection|FCollectionElement $group
 * @var FActiveDataProvider $productsDataProvider
 * @var ProductSection|FCollectionElement $selectedSection
 */
?>
<?php $this->compare->ajaxUpdate('js-compare-wrapper')?>
<div id="js-compare-wrapper">
  <div class="wrapper">
    <?php $this->renderPartial('/_breadcrumbs');?>

    <div class="nofloat m15">
      <h1 class="uppercase white left"><?php echo Yii::app()->meta->setHeader('Сравнение')?></h1>

      <?php if( isset($selectedSection) ) {?>
        <div class="compare-tabs-list no-overflow">
          <?php foreach($this->compare->getGroups() as $group) {?>
            <?php echo $this->compare->buttonChangeTab(
                $group->name.' ('.$this->compare->countAmountByGroup($group->id).')',
                $group->id,
                array('class' => $selectedSection->id == $group->id ? 'active' : '')
              )?>
          <?php }?>
        </div>
      <?php } else {?>
        <?php echo $this->textBlockRegister('Нет товаров в сравнении', 'Нет товаров для сравнения', null);?>
      <?php }?>
    </div>
    <?php if( isset($selectedSection) ) {?>
      <div class="nofloat">
        <div class="compare-header-wrapper">
          <div class="compare-header compare-content">
            <div class="compare-content-inner">
              <?php
                $this->widget('FListView', array(
                  'htmlOptions' => array('class' => 'vitrine'),
                  'dataProvider' => $productsDataProvider,
                  'itemView' => '/product/_product_block',
                  'columnsCount' => 5,
                  'productDummy' => '<div class="product dummie"></div>',
                  'skin' => 'compare'
                ));
              ?>
            </div>
          </div>
        </div>
      </div>
    <?php }?>
  </div>

  <?php if( isset($selectedSection) ) {?>
    <div class="white-body pre-footer">
    <div class="wrapper">
      <div class="nofloat">
        <div class="compare-body">
          <table class="zero compare-table compare-table-header">
            <?php foreach($parametersCompare as $parameterCompare) {?>
              <tr><td><span><?php echo $parameterCompare->name?></span></td></tr>
            <?php }?>
          </table>

          <div class="compare-content">
            <div class="compare-content-inner">
              <table class="zero compare-table compare-table-body">
                <?php foreach($parametersCompare as $parameterCompare) {?>
                  <tr>
                    <?php foreach($productsDataProvider->getData() as $product) {?>
                      <td><span>
                        <?php foreach($product->parameters as $productParameter) {?>
                          <?php if( $parameterCompare->id !== $productParameter->id ) continue;?>
                          <?php echo isset($productParameter) && !empty($productParameter->value) ? $productParameter->value : ''?>
                        <?php }?>
                      </span></td>
                    <?php }?>
                  </tr>
                <?php }?>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php }?>
</div>