<?php
/**
 * @var SitemapController $this
 */
?>
<div class="wrapper">
<?php $this->renderPartial('/_breadcrumbs') ?>

<div class="caption m20">
  <h1><?php echo Yii::app()->meta->setHeader('Карта сайта') ?></h1>
</div>

<div class="nofloat">
  <div class="l-main">
    <?php $this->widget('FMenu', array('items' => $this->getSiteMapMenu()))?>
   </div>
  <div class="r-main">
    <?php $this->widget('FMenu', array('items' => $this->getMenuBuilder()->getSectionTypeMenu()))?>
  </div>
</div>
</div>
