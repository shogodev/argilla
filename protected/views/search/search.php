<?php
/**
 * @var SearchController $this
 * @var FPagination $pages
 * @var Search $model
 */
?>
<?php $this->renderPartial('//breadcrumbs');?>

<h1><?php echo $this->clip('h1', 'Результаты поиска')?></h1>

<div class="text-container m20 s14">
  Поиск по запросу <span class="bb"><?php echo $model->query?></span>:
</div>

<div class="m50">
  <ol class="s13" start="<?php echo (($model->page * $model->pageSize) + 1);?>">
    <?php $this->widget('FListView', array(
      'id' => 'search_results',
      'htmlOptions' => array('class' => 'm20'),
      'dataProvider' => $model->searchResult,
      'itemView' => '_item',
      'enablePagination' => false,
    ));?>
  </ol>
</div>

<?php if( $pages->getPageCount() ) { ?>
<div class="nofloat">
  <div class="pager fr">
    <?php $this->widget('FLinkPager', array(
      'pages' => $pages,
      'htmlOptions' => array('class' => ''),
    ));?>
  </div>
</div>
<?php } ?>

<p>Поиск осуществлен с использованием&nbsp;&nbsp;<a href="http://yandex.ru">Яndex.Server</a></p>