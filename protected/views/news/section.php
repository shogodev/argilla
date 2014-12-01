<?php
/**
 * @var $this NewsController
 * @var $model NewsSection
 * @var $dataProvider FActiveDataProvider
 */
?>
<div class="wrapper">
  <?php $this->renderPartial('/_breadcrumbs');?>

  <div class="caption m20">
    <h1><?php echo Yii::app()->meta->setHeader($model->name)?></h1>
  </div>

  <?php
  /**
   * @var FListView $listView
   */
    $listView = $this->widget('FListView', array(
      'dataProvider' => $dataProvider,
      'itemView' => '_view',
      'template' => '{items}',
      'htmlOptions' => array('class' => 'news-list'),
      'pagerCssClass' => 'pager fr',
    )); ?>

  <div class="nofloat m20">
    <?php $listView->renderPager()?>
  </div>
</div>
