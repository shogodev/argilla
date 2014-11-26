<?php
/**
 * @var LinkController $this
 * @var FActiveDataProvider $dataProvider
 * @var LinkSection[] $sections
 * @var LinkSection $model
 * @var FFixedPageCountPagination $pagination
 */
?>
<div class="wrapper">
  <?php $this->renderPartial('/_breadcrumbs');?>

  <div class="caption m20">
    <h1><?php echo Yii::app()->meta->setHeader($model->name)?></h1>
  </div>

  <div class="m30">
    <?php $widget = $this->widget('FListView', [
      'enablePagination' => false,
      'itemsTagName' => 'ol',
      'dataProvider' => $dataProvider,
      'itemView' => '_link',
      'template' => '{items}',
      'pagerCssClass' => 'pager fr',
    ]);?>
  </div>

  <div class="nofloat m50">
    <div class="fl" style="padding: 3px 10px 0 26px">Другой раздел</div>
    <div class="fl" style="width: 280px">
      <div class="select-container">
        <?php echo CHtml::dropDownList('', $model->url, CHtml::listData($sections, 'url', 'name'), array('id' => 'site-category'))?>
      </div>
    </div>

    <script>
      $(function(){
        $('#site-category').on('change', function(e){
          document.location.href = $(this).val();
        });
      });
    </script>

    <?php if( $pagination->pageCount ) {?>
    <div class="pager fr">
      <?php //В FLinkPager идет проверка выводить 404 или нет?>
      <?php $this->widget('FLinkPager', array('pages' => $pagination))?>
    </div>
    <?php }?>
  </div>
</div>