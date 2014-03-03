<?php
/**
 * @var ProductController $this
 * @var ProductFilterElementSlider $element
 */
?>

<?php $element->render()?>

<div class="m30">
  <div class="h3 bb s18 m15">Диапазон цен</div>
  <div class="nofloat m25">
    <div class="fl">
      <span class="s14">от</span>
      <input id="filter-price-min" class="slider-inp" type="text" value="<?php echo $element->selectedMin?>" />
    </div>
    <div class="fr">
      <span class="s14">до</span>
      <input id="filter-price-max" class="slider-inp" type="text" value="<?php echo $element->selectedMax?>" />
    </div>
  </div>
  <div class="m25" style="position: relative">
    <div id="filter-price-slider" class="m5"></div>
    <div class="price-tooltip" id="filter-price-tooltip">Выбрано моделей: <span id="filter-tooltip-counter"></span> <a href="#" id="filter-tooltip-button">Показать</a></div>
  </div>

  <script>
    //<![CDATA[
    $(function(){
      $('#filter-price-slider').filterSlider({'ranges' : <?php echo $element->getRanges()?>});
    });
    //]]>
  </script>

</div>