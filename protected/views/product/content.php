<?php
/**
 * @var ProductController $this
 * @var FActiveRecord $model
 * @var array $_data_
 * @var FArrayDataProvider $dataProvider
 */
?>

<div class="wrapper">
  <div class="m10">
    <?php $this->renderPartial('/_breadcrumbs');?>
  </div>
  <div class="nofloat m25 h1 bb">
    <h1 class="fl uppercase m0"><?php echo Yii::app()->meta->setHeader($model->name)?></h1> <span class="products-counter" id="item-count">(<?php echo Yii::app()->format->formatNumber($dataProvider->totalItemCount)?>)</span>
  </div>
  <div class="nofloat">
    <aside id="left">

      <?php $this->renderPartial('_menu', $_data_)?>
      <?php $this->renderPartial('filter/_filters', $_data_);?>

      <?php foreach(Banner::model()->getByCurrentUrlAll('catalog_left') as $banner) {?>
        <?php if( !empty($banner->code) ) {?>
          <?php echo $banner->code;?>
        <?php } else {?>
          <div class="m20">
            <?php echo $banner->render();?>
          </div>
        <?php }?>
      <?php }?>

    </aside>

    <section id="main">
      <?php $this->widget('FListView', array(
        'id' => 'product_list',
        'itemsCssClass' => 'vitrine m20',
        'pagerCssClass' => 'pager fr',
        'dataProvider' => $dataProvider,
        'template' => $this->renderPartial('_list_template', $_data_, true),
        'sorterTemplate' => '_sorting',
        'itemView' => '_product_block',
        'ajaxUpdate' => 'left-filter, item-count',
        'ajaxVar' => null,
        'enableHistory' => true,
        'ajaxType' => 'post',
        'pager' => array(
          'class' => 'FLinkPager',
          'maxButtonCount' => 5,
        )));
      ?>
    </section>
  </div>
</div>