<?php
/**
 * @var ProductController $this
 * @var Product $model
 * @var FActiveDataProvider $similarDataProvider
 * @var FActiveDataProvider $relatedDataProvider
 * @var array $_data_
 */
?>

<div class="menu horizontal-menu anchor-links m30">
  <ul>
    <?php if( !empty($model->notice) ) { ?>
      <li class="active"><a href="#description">Описание</a></li>
    <?php } ?>
    <?php if( $parameters = $model->getParametersCard() ) { ?>
      <li><a href="#params">Характеристики</a></li>
    <?php } ?>
    <?php if( $relatedDataProvider->totalItemCount > 0 ) { ?>
      <li><a href="#alsobuy">С этим товаром покупают</a></li>
    <?php } ?>
    <?php if( $similarDataProvider->totalItemCount > 0 ) { ?>
      <li><a href="#samegoods">Похожие товары</a></li>
    <?php } ?>
    <li><a href="#responses">Отзывы</a></li>
  </ul>
</div>

<?php if( !empty($model->notice) ) { ?>
  <div id="description">
    <div class="h1 bb uppercase caption center m20">Описание</div>
    <div class="text-container m30">
      <?php echo $model->notice?>
    </div>
  </div>
<?php } ?>

<?php $this->renderPartial('one/_parameters', array('parameters' => $parameters))?>

<?php if( $relatedDataProvider->totalItemCount > 0 ) { ?>
  <div id="alsobuy">
    <div class="h1 bb uppercase caption center m20">С этим товаром покупают</div>
    <?php $this->widget('FListView', array(
      'htmlOptions' => array('class' => 'vitrine m30'),
      'dataProvider' => $relatedDataProvider,
      'itemView' => '/product/_product_block',
    ));?>
  </div>
<?php } ?>

<?php if( $similarDataProvider->totalItemCount > 0 ) { ?>
  <div id="samegoods">
    <div class="h1 bb uppercase caption center m20">Похожие товары</div>
    <?php $this->widget('FListView', array(
      'htmlOptions' => array('class' => 'vitrine m30'),
      'dataProvider' => $similarDataProvider,
      'itemView' => '/product/_product_block',
    ));?>
  </div>
<?php } ?>

<div id="responses">
  <div class="h1 bb uppercase caption center m20">Отзывы</div>
  <div class="m30">
    <?php //echo Yii::app()->cackle->comments($model);?>
  </div>
</div>