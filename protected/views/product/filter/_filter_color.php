<?php
/**
 * @var ProductController $this
 * @var ProductFilterElementList $element
 */
?>

<div class="m30">
  <div class="h3 bb s18 m15">Основной цвет</div>
  <?php foreach($element->getItems() as $item) { ?>
  <?php if( !empty($item->notice) ) { ?>
  <label class="color-box color-btn" style="border-color: <?php echo $item->notice?>">
    <?php echo CHtml::checkBox($item->name, $item->isSelected(), array('id' => $item->cssId, 'value' => $item->id, 'class' => 'hidden'));?>
  </label>
  <?php } ?>
  <?php } ?>
</div>

<script>
  //<![CDATA[
  $(function(){
    // При загрузке страницы помечаем выбранные чекбоксы
    $('.color-box input:checked').closest('.color-box').addClass('checked');
  });
  $('.color-box input').on('change', function(e){
    if ( $(this).prop('checked') ) {
      $(this).closest('.color-box').addClass('checked');
    } else {
      $(this).closest('.color-box').removeClass('checked');
    }
  });
  //]]>
</script>