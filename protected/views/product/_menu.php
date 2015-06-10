<?php
/**
 * @var array $menus
 */
?>
<div class="left-filters m20">
  <?php foreach($menus as $menu) {?>
    <?php if( empty($menu['items']) ) continue;?>
    <div class="m60">
      <?php if( !empty($menu['label']) ) {?>
        <div class="s19 uppercase m10"><?php echo $menu['label'];?></div>
      <?php }?>
      <fieldset>
        <?php echo ViewHelper::menu($menu['items']);?>
      </fieldset>
    </div>
  <?php }?>
</div>