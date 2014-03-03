<?php
/**
 * @var ProductController $this
 * @var ProductFilter $filter
 */
?>

<?php if( isset($filter) ) { ?>
<div class="left-filters" id="left-filter">
  <div class="m50">
    <?php $filter->render->registerRemoveElementsScript()?>
    <?php if( $selectedElements = $filter->getSelectedElements() ) { ?>
    <div class="m30">
      <div class="h2 s18 red m20">Вы выбрали</div>
      <?php foreach($selectedElements as $element) { ?>
      <?php foreach($element['items'] as $item) { ?>
        <div class="nofloat m15">
        <?php if( empty($element['id']) ) { ?>
          <span class="fixed-filter-icon"></span>
          <?php echo $item?>
        <?php } else { ?>
          <a href="#" class="remove-filter-btn remove-btn" data-remove="<?php echo $item->cssId;?>"></a>
          <?php echo $item->label?>
          <span class="fr"></span>
        <?php } ?>
        </div>
      <?php } ?>
      <?php } ?>
    </div>
    <?php } ?>

    <?php  echo $filter->render->begin() ?>
    <?php $filter->render->registerOnChangeScript(true)?>

    <?php foreach($filter->getElements(array(ProductFilter::FILTER_PRICE, ProductFilter::FILTER_COLOR)) as $element) { ?>
    <?php echo CHtml::openTag('div', $element->htmlOptions)?>
      <div class="h3 bb s18 m15"><?php echo $element->label?></div>

      <?php foreach($element->getItems() as $item) { ?>
      <div class="m10">
        <?php $item->render()?>
        <span class="fr">(<?php echo $item->amount?>)</span>
      </div>
      <?php } ?>
    <?php echo CHtml::closeTag('div')?>
    <?php } ?>

    <?php if( isset($filter->elements[ProductFilter::FILTER_PRICE]) ) { ?>
      <?php $this->renderPartial('filter/_filter_price', array('element' => $filter->elements[ProductFilter::FILTER_PRICE]))?>
    <?php } ?>

    <?php if( isset($filter->elements[ProductFilter::FILTER_COLOR]) ) { ?>
      <?php $this->renderPartial('filter/_filter_color', array('element' => $filter->elements[ProductFilter::FILTER_COLOR]))?>
    <?php } ?>
  </div>

  <div class="center">
    <input type="submit" class="btn red-btn wide-btn" value="Применить" id="filter-submit" style="display: none">
  </div>

  <?php echo $filter->render->end()?>
</div>
<?php } ?>