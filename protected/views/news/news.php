<?php
/**
 * @var $this NewsController
 * @var $model News
 */
?>
<div class="wrapper">
  <?php $this->renderPartial('/_breadcrumbs');?>

  <div class="caption m20">
    <h1><?php echo Yii::app()->meta->setHeader($model->name)?></h1>
  </div>

  <article>
    <?php if( $model->section_id == NewsSection::ID_NEWS ) {?>
      <time datetime="<?php echo $model->getFormatDate('Y-m-d')?>"><?php echo $model->getFormatDate()?></time>
    <?php }?>

    <div class="text-container">
      <?php echo $model->content?>
    </div>
  </article>
</div>