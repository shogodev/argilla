<?php
/**
 * @var SitemapController $this
 */
?>
<div class="wrapper nofloat">
  <section id="main" class="wide">
    <?php $this->renderPartial('/_breadcrumbs') ?>

    <h1 class="uppercase"><?php echo Yii::app()->meta->setHeader('Карта сайта') ?></h1>

    <div class="text-container m30 sitemap">
      <div class="l-main unhova">
        <div class="m15 bb">Общая информация</div>
        <?php $this->widget('FMenu', array('items' => $this->getSiteMapMenu()))?>
      </div>
      <div class="r-main">
        <div class="m15 bb">Каталог</div>
        <?php $this->widget('FMenu', array('items' => $this->getMenuBuilder()->getSectionTypeMenu()))?>
      </div>
    </div>

  </section>
</div>
