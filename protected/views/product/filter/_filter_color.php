<?php
/**
 * @var ProductController $this
 * @var FilterElementCheckbox $element
 */
?>

<div class="filter-caption m20">Цвет</div>
<div class="no-overflow colors-filter">
  <div class="m10 filter-block-body nofloat">
    <?php foreach($element->getItems() as $item) { ?>
    <?php if( !empty($item->notice) ) { ?>
    <label class="color-box" style="background: <?php echo $item->notice?>">
      <?php echo CHtml::checkBox($item->name, $item->isSelected(), array('id' => $item->cssId, 'value' => $item->id, 'class' => 'hidden'));?>
    </label>
    <?php } ?>
    <?php } ?>
  </div>
</div>

<script>
  //<![CDATA[
  $(function() {
    // Фильтры цветов
    $('.color-box :checked').each(function(){
      $(this).closest('.color-box').addClass('checked');
    });
    $('.color-box').click(function(){
      var self = $(this);
      if ( self.find('input').prop('checked') ) {
        self.addClass('checked');
      } else {
        self.removeClass('checked');
      }
    });
  });
  //]]>
</script>