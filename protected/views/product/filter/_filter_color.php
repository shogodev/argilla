<?php
/**
 * @var ProductController $this
 * @var FilterElementCheckbox $element
 */
?>

<div class="m60">
  <div class="s19 uppercase m10">Цвет</div>
  <div class="color-filter">
    <?php foreach($element->items as $item) { ?>
    <label class="color-box" style="background: <?php echo $item->notice?>">
      <?php echo CHtml::checkBox($item->name, $item->isSelected(), array('id' => $item->cssId, 'value' => $item->id, 'class' => 'hidden'));?>
    </label>
    <?php } ?>
  </div>
  <script>
    $(function() {
      $('.color-box input').each(function() {
        var self = $(this);
        if ( self.prop('checked') ) {
          self.closest('.color-box').addClass('active');
        }
      });

      $('.color-box input').change(function() {
        var self = $(this);
        if ( self.prop('checked') ) {
          self.closest('.color-box').addClass('active');
        } else {
          self.closest('.color-box').removeClass('active');
        }
      });
    });
  </script>
</div>