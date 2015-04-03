<?php
/**
 * @var ProductController $this
 * @var Product $data
 */
?>
<?php $this->compare->ajaxUpdate('js-wrapper-compare-header')?>

<div id="js-wrapper-compare-header">
  <?php if( !$this->compare->isEmpty() ) {?>
    <a href="<?php echo $this->createUrl('compare/index');?>" class="hd-compare-link">Сравнение <span><?php echo $this->compare->count();?></span></a>
  <?php } else {?>

  <?php }?>
</div>