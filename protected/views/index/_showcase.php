<?php
/**
 * @var IndexController $this
 * @var Showcase|ShowcaseTab[] $showcase
 */
?>

<?php if( $showcase->count() ) { ?>
<div id="vitrine-tabs" class="main-tabs wrapper nofloat">
  <div class="main-tabs-menu menu m25">
    <ul>
      <?php foreach($showcase as $tab) { ?>
        <li>
          <a href="<?php echo $this->renderDynamic(array($tab, 'getUrl'));?>">
            <i class="icon card-icon card-icon-<?php echo $tab->index?>"></i> <span><?php echo $tab->name?></span>
          </a>
        </li>
      <?php } ?>
    </ul>
  </div>

  <?php foreach($showcase as $tab) { ?>
    <div id="<?php echo $tab->prefix?>">
      <?php $this->widget('FListView', array(
        'dataProvider' => $tab->getRandomDataProvider(),
        'itemView' => '/product/_product_block',
        'itemsCssClass' => 'omega-four nofloat',
      ));?>
    </div>
  <?php } ?>

</div>
<?php } ?>