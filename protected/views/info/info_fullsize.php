<?php
/**
 * @var FController $this
 * @var Info $model
 * @var CommentForm|null $commentForm
 */
?>
<div class="wrap-info">
  <?php $this->renderPartial('/breadcrumbs');?>
</div>

<div class="wrap">
  <div class="container container_16 nofloat">

    <div class="csc-default">
      <h3><?php echo $this->clip('h1', $model->name)?></h3>
    </div>

    <?php echo $model->content?>

    <?php if( $commentForm !== null ):?>
      <div><?php $commentForm->show();?></div>
    <?php endif;?>
  </div>
</div>