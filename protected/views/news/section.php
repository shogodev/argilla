<?php
/**
 * @var $this NewsController
 * @var $model NewsSection
 * @var $dataProvider FActiveDataProvider
 */
?>
<section id="main">

  <?php $this->renderPartial('/breadcrumbs');?>

  <h1><?php echo $this->clip('h1', $model->name)?></h1>

  <?php $this->widget('FListView', array(
    'dataProvider' => $dataProvider,
    'itemView' => '_view',
    'pagerCssClass' => 'page_filter m20 clearfix',
  )); ?>

</section>