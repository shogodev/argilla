<?php
/**
 * @var BasketController $this
 * @var array $_data_
 */
?>
  <div class="wrapper">
    <?php $this->renderPartial('/_breadcrumbs');?>
  </div>

  <div class="center lightest-grey s18 pre-footer">
    <h1 class="lightest-grey s33 uppercase m35 opensans"><?php echo Yii::app()->meta->setHeader('Корзина пуста');?></h1>

    <div class="m35">
      <?php echo $this->textBlockRegister('Корзина пуста', 'Корзина пуста')?>
    </div>

    <a class="btn red-contour-btn solid-btn rounded-btn white-body-btn h34btn p10btn opensans s15 bb uppercase" href="">Вернуться на главную</a>
  </div>
