<?php
/**
 * @var LinkController $this
 * @var FActiveDataProvider $dataProvider
 * @var LinkSection[] $sections
 * @var LinkSection $model
 */
?>

<div>

  <?php $this->renderPartial('/breadcrumbs');?>

  <h1><?php echo Yii::app()->meta->setHeader($model->name)?></h1>

  <div class="m30">
    <?php $widget = $this->widget('FListView', [
      'itemsTagName' => 'ol',
      'dataProvider' => $dataProvider,
      'itemView' => '_link',
      'template' => '{items}',
      'pagerCssClass' => 'pager fr',
    ]);?>
  </div>

  <div class="nofloat m20">
    <div class="fl" style="padding: 9px 10px 0 26px">Другой раздел</div>
    <div class="fl" style="width: 280px">
      <div class="input-wrap">
        <div class="inp">
          <select id="link-sections">
            <?php foreach($sections  as $section) { ?>
            <option value="<?php echo $section->url;?>"<?php echo $section->url == $model->url ? ' selected="selected"' : ''?>><?php echo $section->name;?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>

    <script>
      $(function(){
        $('#link-sections').on('change', function(e){
          document.location.href = $(this).val();
        });
      });
    </script>

    <?php echo $widget->renderPager()?>

  </div>

</div>