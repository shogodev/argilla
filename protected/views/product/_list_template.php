<?php
/**
 * @var ProductController $this
 * @var FActiveRecord|ProductSection $model
 * @var array $_data_
 * @var Banner[] $banners
 */
?>

<div class="filters-block nofloat m15">
  {sorter}
  <div class="filter-label dummie"></div>
  {pager}
</div>

<?php if( $model instanceof FActiveRecord && ($text = $this->getModelText($model)) && $this->isFirstPage() ) { ?>
  <div class="text-container m20"><?php echo $text?></div>
<?php } ?>

{items}

<div class="filters-block nofloat m15">
  {sorter}
  <div class="filter-label dummie"></div>
  {pager}
</div>

<?php if( ($text = $this->getBottomText()) && $this->isFirstPage() ) { ?>
  <div class="text-container m20"><?php echo $text?></div>
<?php } ?>