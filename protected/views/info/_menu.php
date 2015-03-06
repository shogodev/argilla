<?php
/**
 * @var FController $this
 * @var array $menu
 */
?>
<?php if( !empty($menu) ) { ?>
  <aside id="left">
    <div class="menu left-menu info-menu m30">
      <?php $this->widget('FMenu', array('items' => $menu))?>
    </div>
  </aside>
<?php } ?>