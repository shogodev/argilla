<?php
/**
 * @var ProductController $this
 * @var FActiveRecord $model
 * @var array $_data_
 * @var FArrayDataProvider $dataProvider
 */
?>

<?php $this->renderPartial('/_breadcrumbs');?>

<div class="container-fluid">
  <div class="row-fluid">
    <div class="span10">

      <?php if( !empty($model->notice) ) {?>
      <div class="text-container float-none s12" style="padding: 10px">
        <?php echo $model->notice?>
      </div>
      <?php } ?>

      <div class="m20 nofloat">
        <ul id="bike-list" class="bike-list">
          <?php
          $this->widget('FListView', array(
            'id' => 'vitrine',
            'htmlOptions'   => array('class' => 'm20'),
            'dataProvider'  => $dataProvider,
            'itemView'      => '_product_block',
            'pagerCssClass' => 'page_filter m20 clearfix',
          )); ?>
        </ul>
      </div>

    </div>
  </div>
</div>