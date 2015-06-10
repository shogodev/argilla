<?php
/**
 * @var ProductController $this
 * @var Filter $filter
 */
?>
<?php if( isset($filter) ) { ?>
<div class="s22 bb uppercase m10">Подбор</div>

<div class="left-filters m20" id="left-filter">

  <?php echo $filter->render->begin() ?>
  <?php $filter->render->registerOnChangeScript(true)?>
  <?php $filter->render->registerRemoveElementsScript()?>

  <?php if( $items = $filter->getSelectedItems() ) { ?>
  <div class="filter-caption bb m10">Вы выбрали</div>
  <div class="m20 filter-block-body selected-filters-block">
    <?php foreach($items as $item) { ?>
      <div class="m15 nofloat selected-filter">
        <?php if( !$item->isDisabled() ) { ?>
        <?php $item->renderRemoveButton('')?>
        <?php } ?>
        <span><?php echo $item->label?></span>
      </div>
    <?php } ?>
  </div>
  <?php } ?>

  <?php if( isset($filter->elements[ProductFilterBehavior::FILTER_PRICE]) ) { ?>
    <?php $this->renderPartial('filter/_filter_price', array('element' => $filter->elements[ProductFilterBehavior::FILTER_PRICE]))?>
  <?php } ?>

  <?php if( $color = $filter->getElementByKey(ProductFilterBehavior::FILTER_COLOR) ) { ?>
    <?php $this->renderPartial('filter/_filter_color', array('element' => $color) )?>
  <?php } ?>

  <?php foreach($filter->getElements(true, array(ProductFilterBehavior::FILTER_COLOR, ProductFilterBehavior::FILTER_PRICE)) as $element) { ?>
    <?php echo CHtml::openTag('div', $element->htmlOptions)?>
    <div class="s19 uppercase m10"><?php echo $element->label?></div>
    <fieldset>
    <?php foreach($element->getItems() as $item) { ?>
      <div class="checkbox-field">
        <?php $item->render()?>
        <span class="amount">(<?php echo $item->amount?>)</span>
      </div>
    <?php } ?>
    </fieldset>
    <?php echo CHtml::closeTag('div')?>
  <?php } ?>

  <?php echo $filter->render->end()?>

</div>

<?php } ?>