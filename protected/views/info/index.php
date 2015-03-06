<?php
/**
 * @var FController $this
 * @var Info $model
 * @var array $_data_
 */
?>
<div class="wrapper nofloat">

  <?php $this->renderPartial('_menu', array('menu' => $model->getSiblingsMenu()));?>

  <section id="main">
    <?php $this->renderPartial('/_breadcrumbs');?>

    <h1 class="uppercase m10"><?php echo Yii::app()->meta->setHeader($model->name)?></h1>

    <div class="text-container m20">
      <?php echo $model->content?>
    </div>
  </section>
</div>