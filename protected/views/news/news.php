<?php
/**
 * @var $this NewsController
 * @var $model News
 */
?>
<section id="main">

  <?php $this->renderPartial('/breadcrumbs');?>

  <h1><?php echo $model->name?></h1>

  <article>
    <?php if( $model->image ) { ?>
      <img src="<?php echo $model->image?>" alt="" width="110" class="img-polaroid text-left"/>
    <?php } ?>

    <div class="lead"><?php echo $model->date?></div>
    <?php echo $model->content?>

  </article>

</section>