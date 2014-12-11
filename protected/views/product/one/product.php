<?php
/**
 * @var ProductController $this
 * @var Product $model
 * @var array $_data_
 */
?>

<?php $this->renderPartial('/_breadcrumbs');?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="span10">
      <h1><?php echo Yii::app()->meta->setHeader($model->name)?></h1>

      <?php if( $model->getImages('big') && $model->getImages('middle') ) {?>
      <img width="734" height="448" alt="<?php echo $model->name?>" src="<?php echo $model->getImages('middle')[0]?>" style="display: block;">
      <?php } ?>

      <div><?php echo $model->content?></div>

      <?php echo $this->renderPartial('one/_parameters', $_data_)?>

    </div>
  </div>
</div>


