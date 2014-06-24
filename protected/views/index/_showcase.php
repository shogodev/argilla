<?php
/**
 * @var IndexController $this
 * @var Showcase|Tab[] $showcase
 */
?>
<?php if( $showcase->count() ) { ?>
<div id="vitrine-tabs">

  <ul>
    <?php foreach($showcase as $tab) { ?>
      <li><a href="<?php echo $this->renderDynamic(array($tab, 'getUrl'));?>"><?php echo $tab->name?></a></li>
    <?php } ?>
  </ul>

  <?php foreach($showcase as $tab) { ?>
    <div id="<?php echo $tab->index?>">
      <?php $this->widget('FListView', array(
        'dataProvider' => $tab->getRandomDataProvider(),
        'itemsCssClass' => 'vitrine',
        'itemView' => '/product/_product_block_tablet',
        'skin' => array('limit' => 5)
      ));?>
    </div>
  <?php } ?>

</div>

<script>
  //<![CDATA[
  $(function(){
    $('#vitrine-tabs').tabs();
  });
  //]]>
</script>
<?php } ?>