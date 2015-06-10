<?php
/**
 * @var ProductController $this
 * @var FilterElementSlider $element
 */
?>

<div class="m30">
  <div class="s19 uppercase m10">Цена</div>
  <div class="slider-inputs nofloat m20">
    <span class="s16 slider-inp-label">от</span>
    <input id="filter-price-min" class="inp slider-inp" type="text" value="<?php echo $element->selectedMin?>" />
    <span class="s16 slider-inp-label">до</span>
    <input id="filter-price-max" class="inp slider-inp" type="text" value="<?php echo $element->selectedMax?>" />
    <span class="s16 slider-inp-label">руб.</span>
  </div>
  <div class="m25" style="position: relative">
    <div id="filter-price-slider" class="m10"></div>
    <?php $element->render()?>
    <div class="slider-tooltip" id="filter-price-tooltip">Выбрано товаров: <span id="filter-price-tooltip-counter"></span> <a href="#" id="filter-price-tooltip-button">Показать</a></div>
  </div>
  <script>
    $(function() {
      $('#filter-price-slider').filterSlider({
        'ranges' :  <?php echo json_encode($element)?>,
        'controls' : {
          hiddenInput    : '#filter-price-input',
          minInput       : '#filter-price-min',
          maxInput       : '#filter-price-max',
          tooltip        : '#filter-price-tooltip',
          tooltipButton  : '#filter-price-tooltip-button',
          tooltipCounter : '#filter-price-tooltip-counter',
          filterButton   : '#filter-submit'
        }
      });
    });
  </script>
  <div class="center">
    <button class="btn blue-btn cornered-btn h32btn p20btn s19 condensed uppercase" id="filter-submit" style="display: none">Применить</button>
  </div>
</div>