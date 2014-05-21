<?php
/**
 * @var ProductController $this
 * @var Filter $filter
 */
?>

<?php if( isset($filter) ) { ?>
<div class="left-filter nofloat m15" id="left-filter">

  <?php echo $filter->render->begin() ?>
  <?php $filter->render->registerOnChangeScript(true)?>
  <?php $filter->render->registerRemoveElementsScript()?>

  <?php if( $items = $filter->getSelectedItems() ) { ?>
  <div class="filter-caption m20">Вы выбрали</div>
  <div class="m20 filter-block-body">
    <?php foreach($items as $item) { ?>
      <div class="m15 nofloat selected-filter">
        <?php if( !$item->isDisabled() ) { ?>
        <?php $item->renderRemoveButton('<img src="i/icon-remove.png" alt="" class="fl" />')?>
        <?php } ?>
        <span><?php echo $item->label?></span>
      </div>
    <?php } ?>
  </div>
  <?php } ?>

  <?php if( isset($filter->elements[ProductController::FILTER_PRICE]) ) { ?>
    <?php $this->renderPartial('filter/_filter_price', array('element' => $filter->elements[ProductController::FILTER_PRICE]))?>
  <?php } ?>

  <?php foreach($filter->getElements(array(ProductController::FILTER_COLOR, ProductController::FILTER_PRICE)) as $element) { ?>
    <?php echo CHtml::openTag('div', $element->htmlOptions)?>
    <div class="filter-caption m20"><?php echo $element->label?></div>
    <div class="m20 filter-block-body">
    <?php foreach($element->getItems() as $item) { ?>
      <div class="m20 nofloat checkbox-block">
        <?php $item->render()?>
        <span class="fr nn amount">(<?php echo $item->amount?>)</span>
      </div>
    <?php } ?>
    </div>
    <?php echo CHtml::closeTag('div')?>
  <?php } ?>

  <?php if( isset($filter->elements[ProductController::FILTER_COLOR]) ) { ?>
    <?php $this->renderPartial('filter/_filter_color', array('element' => $filter->elements[ProductController::FILTER_COLOR]))?>
  <?php } ?>

  <?php echo $filter->render->end()?>

</div>

<div class="m30">
  <a href="#" class="btn orange-btn h24btn wide-btn s16 bb" id="left-filter-submit" style="display: none">Применить</a>
</div>
<?php } ?>