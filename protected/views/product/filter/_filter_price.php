<?php
/**
 * @var ProductController $this
 * @var FilterElementSlider $element
 */
?>

<div class="filter-caption m30">Цена <span class="lowercase s14 nn">(руб.)</span></div>
<div class="m25 filter-block-body">
  <div class="nofloat m10">
    <div class="fl helios">
      <label for="left-filter-price-min" class="s16 white">от</label>
      <input id="left-filter-price-min" class="slider-inp inp" type="text" value="<?php echo $element->selectedMin?>" />
    </div>
    <div class="fr">
      <label for="left-filter-price-max" class="s16 white">до</label>
      <input id="left-filter-price-max" class="slider-inp inp" type="text" value="<?php echo $element->selectedMax?>" />
    </div>
  </div>
  <div class="m20" style="position: relative">
    <div id="left-filter-price-slider" class="m5 filter-price-slider"></div>
    <?php $element->render()?>
    <div class="price-tooltip" id="left-filter-price-tooltip">Выбрано товаров: <span id="left-filter-tooltip-counter"></span> <a href="#" id="left-filter-tooltip-button" style="margin-left: 10px">Показать</a></div>
  </div>
  <script>
    //<![CDATA[
    $(function(){
      $('#left-filter-price-slider').filterSlider({
        'ranges'   :  <?php echo json_encode($element)?>,
        'controls' : {
          'hiddenInput'    : '#left-filter-price-input',
          'minInput'       : '#left-filter-price-min',
          'maxInput'       : '#left-filter-price-max',
          'tooltip'        : '#left-filter-price-tooltip',
          'tooltipButton'  : '#left-filter-tooltip-button',
          'tooltipCounter' : '#left-filter-tooltip-counter',
          'filterButton'   : '#left-filter-submit'
        }
      });
    });
    //]]>
  </script>
</div>