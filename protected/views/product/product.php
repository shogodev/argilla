<?php
/**
 * @var ProductController $this
 * @var Product $model
 * @var array $_data_
 */
?>

<?php $this->renderPartial('/breadcrumbs');?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="span2">
      <?php echo $this->renderPartial('_sections_menu', $_data_)?>
    </div>
    <div class="span10">
      <h1><?php echo $this->clip('h1', $model->name)?></h1>

      <?php if( $model->getImages('big') && $model->getImages('middle') ) {?>
      <img width="734" height="448" alt="<?php echo $model->name?>" src="<?php echo $model->getImages('middle')[0]?>" style="display: block;">
      <?php } ?>

      <div><?php echo $model->content?></div>

      <?php echo $this->renderPartial('_parameters', $_data_)?>

    </div>
  </div>
</div>


